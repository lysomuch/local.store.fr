<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_ProductGridInlineEditor
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\ProductGridInlineEditor\Block\Adminhtml;

use Magento\Backend\Block\Template;

class InlineEditor extends Template
{
    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory
     */
    protected $attrsetcollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    protected $attrcollectionFactory;

    /**
     * @var \Magento\CurrencySymbol\Model\System\CurrencysymbolFactory
     */
    protected $symbolSystemFactory;

    /**
     * @var \Magento\Framework\Locale\Format
     */
    protected $localeFormat;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Bss\ProductGridInlineEditor\Helper\Data
     */
    protected $helper;

    /**
     * Custom currency symbol properties
     *
     * @var array
     */
    protected $symbolsData = [];

    /**
     * Action constructor.
     * @param Template\Context $context
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $attrsetcollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attrcollectionFactory
     * @param \Magento\CurrencySymbol\Model\System\CurrencysymbolFactory $symbolSystemFactory
     * @param \Magento\Framework\Locale\Format $localeFormat
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Bss\ProductGridInlineEditor\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $attrsetcollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attrcollectionFactory,
        \Magento\CurrencySymbol\Model\System\CurrencysymbolFactory $symbolSystemFactory,
        \Magento\Framework\Locale\Format $localeFormat,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Bss\ProductGridInlineEditor\Helper\Data $helper,
        array $data = []
    ) {
        $this->attrsetcollectionFactory = $attrsetcollectionFactory;
        $this->attrcollectionFactory = $attrcollectionFactory;
        $this->symbolSystemFactory = $symbolSystemFactory;
        $this->localeFormat = $localeFormat;
        $this->eavConfig = $eavConfig;
        $this->jsonHelper = $jsonHelper;
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getUrlSave()
    {
        return $this->getUrl('productgridinlineeditor/inlineEditor/save');
    }

    /**
     * @return string
     */
    public function getUrlSaveMultiples()
    {
        return $this->getUrl('productgridinlineeditor/inlineEditor/saveMultiples');
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->helper->isEnabled();
    }

    /**
     * @return bool
     */
    public function isMassEdit()
    {
        return $this->helper->isMassEdit();
    }

    /**
     * @return bool
     */
    public function isSingleEditField()
    {
        return $this->helper->isSingleEditField();
    }

    /**
     * @return []
     */
    public function getAttrAllowEdit()
    {
        $collectionAttributeSet = $this->attrsetcollectionFactory->create();
        // '4' is the default type ID for 'catalog_product' entity - see 'eav_entity_type' table)
        $collectionAttributeSet->setEntityTypeFilter(4)->load();

        $attr_sets = [];
        $attrs_options = [];
        $type_allow =  explode(',', $this->helper->getInputTypeAllow());
        if (in_array('text', $type_allow)) {
            array_push($type_allow,'weight','qty');
        }
        if ($collectionAttributeSet->getSize() > 0) {
            foreach ($collectionAttributeSet as $item) {
                $attr_setId = $item->getAttributeSetId();
                $attrAll = $this->getCollectionAttrofAttrSet($attr_setId);
                $attr_set = [];
                foreach ($attrAll as $attr) {
                    if (in_array($attr->getFrontendInput(), $type_allow)) {
                        $attr_set[$attr->getAttributeCode()] = [
                            'attribute_id' => $attr->getAttributeId(),
                            'attribute_code' => $attr->getAttributeCode(),
                            'frontend_input' => $attr->getFrontendInput(),
                            'is_required' => $attr->getIsRequired(),
                            'is_unique' => $attr->getIsUnique(),
                            'is_global' => $attr->getIsGlobal(),
                            'no_allow_type_product' => '',
                        ];

                        if ($attr_set[$attr->getAttributeCode()]['frontend_input'] == 'price') {
                            $attr_set[$attr->getAttributeCode()]['no_allow_type_product'] = 'configurable,grouped,bundle';
                        }
                    }
                    
                    if (!isset($attr_set['qty']) && in_array('qty', $type_allow)) {
                        $attr_set['qty'] = [
                            'attribute_id' => '1511991',
                            'attribute_code' => 'qty',
                            'frontend_input' => 'text',
                            'is_required' => 0,
                            'is_unique' => 0,
                            'is_global' => 0,
                            'no_allow_type_product' => 'configurable,grouped,bundle',
                        ];
                    }
                    if (($attr->getFrontendInput() == 'boolean' || $attr->getFrontendInput() == 'select' || $attr->getFrontendInput() == 'multiselect')
                        && !isset($attrs_options[$attr->getAttributeCode()])) {
                        $attribute = $this->eavConfig->getAttribute('catalog_product', $attr->getAttributeCode());
                        $options = $attribute->getSource()->getAllOptions();
                        $attrs_options[$attr->getAttributeCode()] = $options;
                    }
                }
                $attr_sets[$attr_setId] = $attr_set;
            }
        }

        // convert array to json
        $json_attr_sets = $this->jsonHelper->jsonEncode($attr_sets);
        $json_attrs_options = $this->jsonHelper->jsonEncode($attrs_options);

        return ['attr_sets' => $json_attr_sets, 'attrs_options' => $json_attrs_options];
    }

    /**
     * @param $attrsetId
     * @return \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection
     */
    private function getCollectionAttrofAttrSet($attrsetId)
    {
        $collectionAttrofAttrSet = $this->attrcollectionFactory->create();
        $collectionAttrofAttrSet->setAttributeSetFilter($attrsetId)
            ->addVisibleFilter()
            ->load();
        return $collectionAttrofAttrSet;
    }

    /**
     * Returns Custom currency symbol properties
     *
     * @return array
     */
    public function getCurrencySymbolsData()
    {
        if (!$this->symbolsData) {
            $this->symbolsData = $this->symbolSystemFactory->create()->getCurrencySymbolsData();
        }
        return $this->jsonHelper->jsonEncode($this->symbolsData);
    }

    /**
     * @return string
     */
    public function getPriceFormat()
    {
        return $this->localeFormat->getPriceFormat();
    }
}
