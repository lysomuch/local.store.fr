<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageplaza\Affiliate\Model\Total\Order\Creditmemo;

class Affiliate extends \Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal
{
    /**
     * Collect Creditmemo subtotal
     *
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        $order = $creditmemo->getOrder();
        $sum = $order->getGrandTotal() - $order->getAffiliateDiscountAmount();
        $rate = $creditmemo->getGrandTotal() / ($sum);

        $commission = $order->getAffiliateCommission();
        if($commission){
            $commission = \Magento\Framework\App\ObjectManager::getInstance()->get('\Mageplaza\Affiliate\Helper\Data')->unserialize($commission);
            $refundCommission = [];
            foreach($commission as $id => $com){
                $refundCommission[$id] = $creditmemo->roundPrice($com * $rate, 'commission', true);
            }

            $creditmemo->setAffiliateCommission($refundCommission);
        }

        $baseOrderDiscount = $order->getBaseAffiliateDiscountAmount();
        if($baseOrderDiscount) {
            $orderDiscount = $order->getAffiliateDiscountAmount();

            $affiliateDiscount     = $creditmemo->roundPrice($orderDiscount * $rate, 'regular', true);
            $baseAffiliateDiscount = $creditmemo->roundPrice($baseOrderDiscount * $rate, 'base', true);

            $baseInvoiceDiscount = 0;
            $invoiceDiscount = 0;
            foreach ($creditmemo->getOrder()->getInvoiceCollection() as $previousInvoice) {
                $baseInvoiceDiscount += $previousInvoice->getBaseAffiliateDiscountAmount();
                $invoiceDiscount += $previousInvoice->getAffiliateDiscountAmount();
            }
            foreach ($creditmemo->getOrder()->getCreditmemosCollection() as $previousCreditmemo) {
                $baseInvoiceDiscount -= $previousCreditmemo->getBaseAffiliateDiscountAmount();
                $invoiceDiscount -= $previousCreditmemo->getAffiliateDiscountAmount();
            }

            $affiliateDiscount     = max($invoiceDiscount, $affiliateDiscount);
            $baseAffiliateDiscount = max($baseInvoiceDiscount, $baseAffiliateDiscount);

            $creditmemo->setAffiliateDiscountAmount($affiliateDiscount);
            $creditmemo->setBaseAffiliateDiscountAmount($baseAffiliateDiscount);

            $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $affiliateDiscount);
            $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseAffiliateDiscount);
        }

        return $this;
    }
}
