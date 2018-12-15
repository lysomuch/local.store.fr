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
 * @package    Bss_TimeCountdown
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\TimeCountdown\Helper;

use Magento\Framework\App\Helper\Context;

class ModuleConfig extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $datetime;

    /**
     * ModuleConfig constructor.
     * @param Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
    )
    {
        $this->storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->datetime = $date;
        parent::__construct($context);
    }

    /**
     * @return string
     */
    public function getDateTimeZone () {
        $dateTimeZone = $this->datetime->date()->format('Y-m-d H:i:s');
        return $dateTimeZone;
    }

    /**
     * @return mixed
     */
    public function isEnableModuleTimeCountdown () {
        return $this->_scopeConfig->getValue(
            'timeCountdown/enable_module_group/enable_module_field',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param null $product
     * @return string
     */
    public function getSelectedTimerStart ($product = null) {
        if($product != null) {
            return $product->getResource()->getAttributeRawValue(
                $product->getId(),
                'select_timecountdown_start',
                $this->getStoreId()
            );
        }
        return '';
    }

    /**
     * @param null $product
     * @return string
     */
    public function getSelectedTimerEnd ($product = null) {
        if($product != null) {
            return $product->getResource()->getAttributeRawValue(
                $product->getId(),
                'select_timecountdown_end',
                $this->getStoreId()
            );
        }
        return '';
    }

    /**
     * @return mixed
     */
    public function enableMessSaleValue ()
    {
        return $this->_scopeConfig->getValue(
            'timeCountdown/message_sale/enable_message_sale_value',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getMessSaleValue ()
    {
        return $this->_scopeConfig->getValue(
            'timeCountdown/message_sale/messSaleValue',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getColorMessSaleValue ()
    {
        return $this->_scopeConfig->getValue(
            'timeCountdown/message_sale/colorMessSaleValue',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getFontSizeMessSaleValue ()
    {
        return $this->_scopeConfig->getValue(
            'timeCountdown/message_sale/fontSizeMessSaleValue',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function enableMessSalePercent ()
    {
        return $this->_scopeConfig->getValue(
            'timeCountdown/message_sale/enable_message_sale_percent',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getMessSalePercent ()
    {
        return $this->_scopeConfig->getValue(
            'timeCountdown/message_sale/messSalePercent',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getColorMessSalePercent ()
    {
        return $this->_scopeConfig->getValue(
            'timeCountdown/message_sale/colorMessSalePercent',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getFontSizeMessSalePercent ()
    {
        return $this->_scopeConfig->getValue(
            'timeCountdown/message_sale/fontSizeMessSalePercent',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getTitlePage () {
        return $this->_scopeConfig->getValue(
            'timeCountdown/bss_seo/title_page',
             \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function isUsePage () {
        return $this->_scopeConfig->getValue(
            'timeCountdown/bss_seo/isUsePage',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }


    /**
     * @return string
     */
    public function getLabelKey() {
        $enableModule = $this->isEnableModuleTimeCountdown();
        $isUsePage = $this->isUsePage();
        if($enableModule && $isUsePage) {
            return $this->_scopeConfig->getValue(
                'timeCountdown/bss_seo/url_label',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
        return '';
    }

    /**
     * @return mixed
     */
    public function getUrlKey () {
        return $this->_scopeConfig->getValue(
            'timeCountdown/bss_seo/url_key',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getMetaTitle () {
        return $this->_scopeConfig->getValue(
            'timeCountdown/bss_seo/meta_title', 
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getMetaKeyword() {
        return $this->_scopeConfig->getValue(
            'timeCountdown/bss_seo/meta_key', 
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getMetaDesc () {
        return $this->_scopeConfig->getValue(
            'timeCountdown/bss_seo/meta_desc',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * @return mixed
     */
    public function isEnableStartTime () {
        return $this->_scopeConfig->getValue(
            'timeCountdown/startTime/enable_start', 
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function numDayStart () {
        return $this->_scopeConfig->getValue(
            'timeCountdown/startTime/numDayStart',
             \Magento\Store\Model\ScopeInterface::SCOPE_STORE
         );
    }

    /**
     * @return mixed
     */
    public function isDisplayCatelogPageStart () {
        return $this->_scopeConfig->getValue(
            'timeCountdown/startTime/displayCatalogPageStart',
             \Magento\Store\Model\ScopeInterface::SCOPE_STORE
         );
    }

    /**
     * @return mixed
     */
    public function isCatalogMessStart () {
        return $this->_scopeConfig->getValue(
            'timeCountdown/startTime/isCatalogMessStart', 
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function catalogMessStart () {
        return $this->_scopeConfig->getValue(
            'timeCountdown/startTime/catalogPageMessStart',
             \Magento\Store\Model\ScopeInterface::SCOPE_STORE
         );
    }

    /**
     * @return mixed
     */
    public function colorCatalogMessStart () {
        return $this->_scopeConfig->getValue(
            'timeCountdown/startTime/colorCatalogMessStart',
             \Magento\Store\Model\ScopeInterface::SCOPE_STORE
         );
    }

    /**
     * @return mixed
     */
    public function fontSizeCatalogMessStart () {
        return $this->_scopeConfig->getValue(
            'timeCountdown/startTime/fontSizeCatalogMessStart',
             \Magento\Store\Model\ScopeInterface::SCOPE_STORE
         );
    }

    /**
     * @return mixed
     */
    public function catalogStyleStart () {
        return $this->_scopeConfig->getValue(
            'timeCountdown/startTime/catalogStyleTimeStart',
             \Magento\Store\Model\ScopeInterface::SCOPE_STORE
         );
    }

    /**
     * @return mixed
     */
    public function isDisplayProductPageStart () {
        return $this->_scopeConfig->getValue(
            'timeCountdown/startTime/displayProductPageStart',
             \Magento\Store\Model\ScopeInterface::SCOPE_STORE
         );
    }

    /**
     * @return mixed
     */
    public function isProductMessStart () {
        return $this->_scopeConfig->getValue(
            'timeCountdown/startTime/isProductMessStart',
             \Magento\Store\Model\ScopeInterface::SCOPE_STORE
         );
    }

    /**
     * @return mixed
     */
    public function productMessStart () {
        return $this->_scopeConfig->getValue(
            'timeCountdown/startTime/productPageMessStart', 
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function colorProductMessStart () {
        return $this->_scopeConfig->getValue(
            'timeCountdown/startTime/colorProductMessStart', 
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function fontSizeProductMessStart () {
        return $this->_scopeConfig->getValue(
            'timeCountdown/startTime/fontSizeProductMessStart', 
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function productStyleStart () {
        return $this->_scopeConfig->getValue(
            'timeCountdown/startTime/productStyleTimeStart', 
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function isEnableEndTime () {
        return $this->_scopeConfig->getValue(
            'timeCountdown/endTime/enable_end',
             \Magento\Store\Model\ScopeInterface::SCOPE_STORE
         );
    }

    /**
     * @return mixed
     */
    public function numDayEnd () {
        return $this->_scopeConfig->getValue(
            'timeCountdown/endTime/numDayEnd', 
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function isDisplayCatelogPageEnd () {
        return $this->_scopeConfig->getValue(
            'timeCountdown/endTime/displayCatalogPageEnd',
             \Magento\Store\Model\ScopeInterface::SCOPE_STORE
         );
    }

    /**
     * @return mixed
     */
    public function isCatalogMessEnd () {
        return $this->_scopeConfig->getValue(
            'timeCountdown/endTime/isCatalogMessEnd', 
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function catalogMessEnd () {
        return $this->_scopeConfig->getValue(
            'timeCountdown/endTime/catalogPageMessEnd', 
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function colorCatalogMessEnd () {
        return $this->_scopeConfig->getValue(
            'timeCountdown/endTime/colorCatalogMessEnd', 
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function fontSizeCatalogMessEnd () {
        return $this->_scopeConfig->getValue(
            'timeCountdown/endTime/fontSizeCatalogMessEnd', 
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function catalogStyleEnd () {
        return $this->_scopeConfig->getValue(
            'timeCountdown/endTime/catalogStyleTimeEnd', 
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return array
     */
    public function getAllConfig () {
        return [
            'is_enable_module' => $this->isEnableModuleTimeCountdown(),

            'select_start' => $this->getSelectedTimerStart(),
            'select_end' => $this->getSelectedTimerEnd(),

            'enable_mess_sale_value' => $this->enableMessSaleValue(),
            'mess_sale_value' => $this->getMessSaleValue(),
            'color_mess_sale' => $this->getColorMessSaleValue(),
            'font_size_mess_sale' => $this->getFontSizeMessSaleValue(),

            'enable_mess_sale_percent' => $this->enableMessSalePercent(),
            'mess_percent' => $this->getMessSalePercent(),
            'color_mess_percent' => $this->getColorMessSalePercent(),
            'font_size_mess_percent' => $this->getFontSizeMessSalePercent(),

            'title_page' => $this->getTitlePage(),
            'url_key' => $this->getUrlKey(),
            'meta_title' => $this->getMetaTitle(),
            'meta_keyword' => $this->getMetaKeyword(),
            'meta_desc' => $this->getMetaDesc(),

            'is_enable_start' => $this->isEnableStartTime(),
            'num_day_start' => $this->numDayStart(),

            'is_catalog_start' => $this->isDisplayCatelogPageStart(),
            'is_mess_catalog_start' => $this->isCatalogMessStart(),
            'mess_catalog_start' => $this->catalogMessStart(),
            'color_mess_catalog_start' => $this->colorCatalogMessStart(),
            'font_size_mess_catalog_start' => $this->fontSizeCatalogMessStart(),
            'style_catalog_start' => $this->catalogStyleStart(),

            'is_product_start' => $this->isDisplayProductPageStart(),
            'is_mess_product_start' => $this->isProductMessStart(),
            'mess_product_start' => $this->productMessStart(),
            'color_mess_product_start' => $this->colorProductMessStart(),
            'font_size_mess_product_start' => $this->fontSizeProductMessStart(),
            'styles_product_start' => $this->productStyleStart(),


            'is_enable_end' => $this->isEnableEndTime(),
            'num_day_end' => $this->numDayEnd(),

            'is_catalog_end' => $this->isDisplayCatelogPageEnd(),
            'is_mess_catalog_end' => $this->isCatalogMessEnd(),
            'mess_catalog_end' => $this->catalogMessEnd(),
            'color_mess_catalog_end' => $this->colorCatalogMessEnd(),
            'font_size_mess_catalog_end' => $this->fontSizeCatalogMessEnd(),
            'style_catalog_end' => $this->catalogStyleEnd(),

            'is_product_end' => $this->isDisplayProductPageEnd(),
            'is_mess_product_end' => $this->isProductMessEnd(),
            'mess_product_end' => $this->productMessEnd(),
            'color_mess_product_end' => $this->colorProductMessEnd(),
            'font_size_mess_product_end' => $this->fontSizeProductMessEnd(),
            'styles_product_end' => $this->productStyleEnd(),

        ];
    }


    /**
     * @return mixed
     */
    public function isDisplayProductPageEnd () {
        return $this->_scopeConfig->getValue(
            'timeCountdown/endTime/displayProductPageEnd', 
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function isProductMessEnd () {
        return $this->_scopeConfig->getValue(
            'timeCountdown/endTime/isProductMessEnd', 
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function productMessEnd () {
        return $this->_scopeConfig->getValue(
            'timeCountdown/endTime/productPageMessEnd',
             \Magento\Store\Model\ScopeInterface::SCOPE_STORE
         );
    }

    /**
     * @return mixed
     */
    public function colorProductMessEnd () {
        return $this->_scopeConfig->getValue(
            'timeCountdown/endTime/colorProductMessEnd', 
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function fontSizeProductMessEnd () {
        return $this->_scopeConfig->getValue(
            'timeCountdown/endTime/fontSizeProductMessEnd', 
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function productStyleEnd () {
        return $this->_scopeConfig->getValue(
            'timeCountdown/endTime/productStyleTimeEnd', 
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
