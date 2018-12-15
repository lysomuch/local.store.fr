<?php
/**
 * Created by PhpStorm.
 * User: cristen
 * Date: 2018/6/25
 * Time: 11:03
 */

namespace Silk\OfflineShipping\Model\Carrier;


class Freeshipping
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->_checkoutSession = $checkoutSession;
    }

    public function afterCollectRates(\Magento\OfflineShipping\Model\Carrier\Freeshipping $freeshipping, $result)
    {
        if ($freeshipping->getConfigFlag('active')) {
            $freeShippingSubTotal = $freeshipping->getConfigData('free_shipping_subtotal');
            // Get cart grand total from checkout session.
            $baseSubtotal = $this->_checkoutSession->getQuote()->getSubtotalWithDiscount();
            // Validate subtoal should be empty or Zero.
            if(!empty($baseSubtotal) && !empty($freeShippingSubTotal) && $baseSubtotal < $freeShippingSubTotal) {
                return false;
            }
        }
        return $result;
    }
}