<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Feed
 */


namespace Amasty\Feed\Model\Export\RowCustomizer;

use Amasty\Feed\Model\Export\Product;
use Magento\Catalog\Pricing\Price\FinalPrice as CatalogFinalPrice;
use Magento\CatalogImportExport\Model\Export\RowCustomizerInterface;

class Price implements RowCustomizerInterface
{
    protected $_prices = [];

    protected $_storeManager;

    protected $_export;

    protected $_calculationCollectionFactory;

    protected $_objectConverter;

    /**
     * @var \Magento\Tax\Model\Calculation
     */
    private $calculation;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Product $export,
        \Magento\Tax\Model\ResourceModel\Calculation\CollectionFactory $calculationCollectionFactory,
        \Magento\Tax\Model\Calculation $calculation,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Convert\DataObject $objectConverter
    ) {
        $this->_storeManager = $storeManager;
        $this->_export = $export;
        $this->_calculationCollectionFactory = $calculationCollectionFactory;
        $this->_objectConverter = $objectConverter;
        $this->calculation = $calculation;
        $this->request = $request;
    }

    /**
     * @inheritdoc
     */
    public function prepareData($collection, $productIds)
    {
        if ($this->_export->hasAttributes(Product::PREFIX_PRICE_ATTRIBUTE)) {

            $collection->applyFrontendPriceLimitations();

            $storeId = $this->request->getParam('store_id');

            foreach ($collection as &$item) {
                $addressRequestObject = $this->calculation->getDefaultRateRequest($storeId);
                $addressRequestObject->setProductClassId($item->getTaxClassId());

                $taxPercent = $this->calculation->getRate($addressRequestObject);
                $finalPrice = $item->getPriceInfo()->getPrice(CatalogFinalPrice::PRICE_CODE)->getValue();

                if ($finalPrice === null) {
                    $item->load($item->getId());
                    $finalPrice = $item->getPriceInfo()->getPrice(CatalogFinalPrice::PRICE_CODE)->getValue();
                }

                $this->_prices[$item['entity_id']] = [
                    'final_price' => $finalPrice,
                    'price' => $item['price'],
                    'min_price' => $item['min_price'],
                    'max_price' => $item['max_price'],
                    'tax_price' => $taxPercent != 0 ?
                        ($item['price'] + $item['price'] * $taxPercent / 100)
                        : $item['price'],
                    'tax_final_price' => $taxPercent != 0 ?
                        ($finalPrice + $finalPrice * $taxPercent / 100)
                        : $finalPrice
                ];
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

        $customData[Product::PREFIX_PRICE_ATTRIBUTE]
            = isset($this->_prices[$productId]) ? $this->_prices[$productId]
            : [];

        return $dataRow;
    }

    /**
     * @inheritdoc
     */
    public function getAdditionalRowsCount($additionalRowsCount, $productId)
    {
        return $additionalRowsCount;
    }
}
