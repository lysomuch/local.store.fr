<?php
/**
 * Created by PhpStorm.
 * User: cristen
 * Date: 2018/6/25
 * Time: 11:03
 */

namespace Silk\OfflineShipping\Model\Carrier;


class Flatrate
{
    const XML_PATH_FREE_SHIPPING_SUBTOTAL = "carriers/freeshipping/free_shipping_subtotal";
    const XML_PATH_FREE_SHIPPING_ACTIVE = "carriers/freeshipping/active";

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        $this->_checkoutSession = $checkoutSession;
        $this->_scopeConfig = $scopeConfig;
    }

    public function afterCollectRates(\Magento\OfflineShipping\Model\Carrier\Flatrate $flatRate, $result)
    {
        $scopeId = $this->_storeManager->getStore()->getId();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        // Get MOA value from system configuration.
        $freeShippingActive = $this->_scopeConfig->getValue(self::XML_PATH_FREE_SHIPPING_ACTIVE, $storeScope, $scopeId);
        // freeshipping disabled
        if( ! $freeShippingActive){
            return $result;
        }
        // Get MOA value from system configuration.
        $freeShippingSubTotal = $this->_scopeConfig->getValue(self::XML_PATH_FREE_SHIPPING_SUBTOTAL, $storeScope, $scopeId);
        // Get cart grand total from checkout session.
        $baseSubtotal = $this->_checkoutSession->getQuote()->getSubtotalWithDiscount();
        // Validate subtoal should be empty or Zero.
        if(!empty($baseSubtotal) && !empty($freeShippingSubTotal) && $baseSubtotal >= $freeShippingSubTotal) {
            return false;
        }

        return $result;
    }
}