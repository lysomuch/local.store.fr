<?php

/**
 * User: Bob song <song01140228@163.com>
 * @date 18-5-24 上午11:13
 */


namespace Silk\Quote\Model\Plugin\Quote;

use Closure;

class QuoteToOrderItem
{
    /**
     * @param \Magento\Quote\Model\Quote\Item\ToOrderItem $subject
     * @param Closure $proceed
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param array $additional
     * @return \Magento\Sales\Model\Order\Item
     */
    public function aroundConvert(
        \Magento\Quote\Model\Quote\Item\ToOrderItem $subject,
        Closure $proceed,
        \Magento\Quote\Model\Quote\Item\AbstractItem $item,
        $additional = []
    ) {
        /** @var $orderItem \Magento\Sales\Model\Order\Item */
        $orderItem = $proceed($item, $additional);
        $orderItem->setIsNewCustomer($item->getIsNewCustomer());
        $orderItem->setIsGiftProduct($item->getIsGiftProduct());
        $orderItem->setGiftQty($item->getGiftQty());
        return $orderItem;
    }

}