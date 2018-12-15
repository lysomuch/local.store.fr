<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile
namespace Silk\Gift\Block\Adminhtml\Product;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;


    /**
     * @var \Silk\Gift\Model\Product
     */
    protected $_giftProduct;

    /**
     * Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Silk\Gift\Model\Product $giftProduct
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Silk\Gift\Model\Product $giftProduct,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_giftProduct = $giftProduct;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Initialize grid
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('giftProductGrid');
    }

    /**
     * Prepare collection
     *
     * @return \Magento\Review\Block\Adminhtml\Grid
     */
    protected function _prepareCollection()
    {
        $collection = $this->_giftProduct->getCollection();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    protected function _getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        return $this->_storeManager->getStore($storeId);
    }

    /**
     * Prepare grid columns
     *
     * @return \Magento\Backend\Block\Widget\Grid
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'gift_id',
            [
                'header' => __('ID'),
                'index' => 'gift_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'product_id',
            [
                'header' => __('Product'),
                'index' => 'product_id',
                'filter' => false,
                'renderer' => 'Silk\Gift\Block\Adminhtml\Product\Renderer\Product'
            ]
        );

        $this->addColumn(
            'title',
            [
                'header' => __('Title'),
                'index' => 'title',
            ]
        );

        $this->addColumn(
            'qty',
            [
                'header' => __('Qty'),
                'type' => 'number',
                'index' => 'qty',
            ]
        );

        $store = $this->_getStore();
        $this->addColumn(
            'min_price',
            [
                'header' => __('Min Price'),
                'type' => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index' => 'min_price',
            ]
        );

        $this->addColumn(
            'max_price',
            [
                'header' => __('Max Price'),
                'type' => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index' => 'max_price',
            ]
        );

        $this->addColumn(
            'start_date',
            [
                'header' => __('Start Date'),
                'type' => 'date',
                'index' => 'start_date',
                'header_css_class' => 'col-date',
                'column_css_class' => 'col-date'
            ]
        );

        $this->addColumn(
            'end_date',
            [
                'header' => __('End Date'),
                'type' => 'date',
                'index' => 'end_date',
                'header_css_class' => 'col-date',
                'column_css_class' => 'col-date'
            ]
        );

        $this->addColumn(
            'is_active',
            [
                'header' => __('Active'),
                'type' => 'options',
                'index' => 'is_active',
                'options' => array(0 => __('Not active'), 1 => __('Active'))
            ]
        );


        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

    /**
     * Prepare grid mass actions
     *
     * @return void
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('gift_id');
        $this->setMassactionIdFilter('gift_id');
        $this->setMassactionIdFieldOnlyIndexValue(true);
        $this->getMassactionBlock()->setFormFieldName('gift_ids');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl(
                    '*/*/Delete',
                    []
                ),
                'confirm' => __('Are you sure?')
            ]
        );
    }

    /**
     * Get row url
     *
     * @param \Magento\Review\Model\Review|\Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            'gift/product/edit',
            [
                'id' => $row->getGiftId(),
                'productId' => $this->getProductId()
            ]
        );
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('gift/product/index', ['_current' => true]);
    }
}
