<?php

/**
 * User: Bob song <song01140228@163.com>
 * @date 18-5-24 上午11:08
 */


namespace Silk\Quote\Model\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class QuoteSubmitToOrderObserver implements ObserverInterface
{

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /* @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getData('order');
        /* @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getData('quote');

        $order->setHasGiftProduct($quote->getHasGiftProduct());

        return $this;
    }
}