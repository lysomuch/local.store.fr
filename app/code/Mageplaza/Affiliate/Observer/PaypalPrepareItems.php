<?php

namespace Mageplaza\Affiliate\Observer;

use Magento\Framework\Event\ObserverInterface;

class PaypalPrepareItems implements ObserverInterface
{
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$cart = $observer->getEvent()->getCart();
		$salesEntity = $cart->getSalesModel();
		$discount = abs($salesEntity->getDataUsingMethod('affiliate_discount_amount'));
		if ($discount > 0.0001) {
 		     $cart->addCustomItem('Affiliate Discount', 1, -1.00 * $discount);
		}
	}

}
