<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Feed
 */


namespace Amasty\Feed\Model\Export\RowCustomizer;

use Magento\CatalogImportExport\Model\Export\RowCustomizerInterface;

class Relation implements RowCustomizerInterface
{
    protected $_storeManager;

    protected $_parent2child;

    protected $_child2parent;

    protected $_export;

    protected $_entityFactory;

    protected $_parentData;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Amasty\Feed\Model\Export\Product $export,
        \Magento\ImportExport\Model\Export\Entity\Factory $entityFactory
    ) {
        $this->_storeManager = $storeManager;
        $this->_export = $export;
        $this->_entityFactory = $entityFactory;
    }

    /**
     * @inheritdoc
     */
    public function prepareData($collection, $productIds)
    {
        $this->_parentData = [];
        $parentAttributes = array_merge_recursive(
            $this->_export->getAttributes(),
            [
                'product' => [
                    'product_id' => 'product_id'
                ]
            ]
        );

        if (count($parentAttributes) > 0) {
            $parent2child = [];
            $child2parent = [];
            $select = $collection->getConnection()
                ->select()
                ->from(
                    ['r' => $collection->getTable('catalog_product_relation')],
                    ['r.parent_id', 'r.child_id']
                )
                ->where('r.child_id IN(?)', $productIds);


            foreach ($collection->getConnection()->fetchAll($select) as $row) {

                if (isset($row['parent_id'])) {
                    $parent2child[$row['parent_id']] = [];
                }

                if (isset($row['child_id'])) {
                    $child2parent[$row['child_id']] = [];
                }

                $parent2child[$row['parent_id']][$row['child_id']] = $row['child_id'];
                $child2parent[$row['child_id']][$row['parent_id']] = $row['parent_id'];
            }

            $this->_parent2child = $parent2child;
            $this->_child2parent = $child2parent;


            $parentsExport = $this->_entityFactory->create('\Amasty\Feed\Model\Export\Product');

            $exportData = $parentsExport
                ->setAttributes($parentAttributes)
                ->setStoreId($collection->getStoreId())
                ->exportParents(array_keys($this->_parent2child));

            foreach ($exportData as $item) {
                if (array_key_exists('product_id', $item)) {
                    $this->_parentData[$item['product_id']] = $item;
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function addHeaderColumns($columns)
    {
        return $columns;
    }

    /**
     * @inheritdoc
     */
    public function addData($dataRow, $productId)
    {
        $customData = &$dataRow['amasty_custom_data'];

        if (isset($this->_child2parent[$productId])) {
            $parentId = end($this->_child2parent[$productId]);

            if (isset($this->_parentData[$parentId])) {
                $this->_fillParentData($dataRow, $this->_parentData[$parentId]);

                $customData['parent_data'] = $this->_parentData[$parentId];
            }
        }

        return $dataRow;
    }

    /**
     * @param array $dataRow
     * @param array $parentRow
     */
    protected function _fillParentData(&$dataRow, $parentRow)
    {
        foreach ($dataRow as $key => $value) {
            if (isset($parentRow[$key])) {
                if (is_array($value)) {
                    $this->_fillParentData($dataRow[$key], $parentRow[$key]);
                } else {
                    if ($value == "" && !empty($parentRow[$key])) {
                        $dataRow[$key] = $parentRow[$key];
                    }
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function getAdditionalRowsCount($additionalRowsCount, $productId)
    {
        return $additionalRowsCount;
    }
}
