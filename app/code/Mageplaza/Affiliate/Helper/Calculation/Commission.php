<?php

namespace Mageplaza\Affiliate\Helper\Calculation;

use Mageplaza\Affiliate\Helper\Calculation;

class Commission extends Calculation
{
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    )
    {
        parent::collect($quote, $shippingAssignment, $total);
        if (!$this->canCalculate()) {
            return $this;
        }

        $baseGrandTotal   = array_sum($total->getAllBaseTotalAmounts());
        if(!$this->getConfigValue('affiliate/commission/by_tax')){
            $baseGrandTotal -= $total->getBaseTaxAmount();
        }
        if(!$this->getConfigValue('affiliate/commission/by_shipping')){
            $baseGrandTotal -= $total->getBaseShippingAmount();
        }
        if(!$this->getConfigValue('affiliate/commission/by_affiliate_discount')){
            $baseGrandTotal -= $total->getBaseTotalAmount('affiliate_discount');
        }
        $commissionResult = [];
        $fieldSubfix      = $this->hasFirstOrder() ? '_second' : '';

        $account = $this->registry->registry('mp_affiliate_account');
        $tree = $this->getAffiliateTree($account);
        foreach ($this->getAvailableCampaign($account) as $campaign) {
            $commissions = $campaign->getCommission();
            $campaigns[] = $campaign->getId();
            foreach ($commissions as $key => $tier) {
                if (!isset($tree[$key])) {
                    break;
                }
                $tierId = $tree[$key];
                if (!isset($commissionResult[$tierId])) {
                    $commissionResult[$tierId] = 0;
                }
                $commission = $tier['value' . $fieldSubfix];
                switch ($tier['type' . $fieldSubfix]) {
                    case \Mageplaza\Affiliate\Block\Adminhtml\Campaign\Edit\Tab\Commissions\Arraycommission::TYPE_FIXED:
                        $commissionResult[$tierId] += $this->priceCurrency->round($commission);
                        break;
                    case \Mageplaza\Affiliate\Block\Adminhtml\Campaign\Edit\Tab\Commissions\Arraycommission::TYPE_SALE_PERCENT:
                        $commissionResult[$tierId] += $this->priceCurrency->round($baseGrandTotal * $commission / 100);
                        break;
                    default:
                        break;
                }
            }
        }

        $total->setAffiliateCommission($this->serialize($commissionResult));
        $total->setAffiliateCampaigns(implode(',',$campaigns));


        return $this;
    }

    public function getAffiliateTree($account, $numOfTier = null)
    {
        $tree = explode('/', $account->getTree());

        if ($numOfTier) {
            while (sizeof($tree) > $numOfTier) {
                array_shift($tree);
            }
        }

        $treeResult = [];
        $tier = 1;
        while (sizeof($tree)) {
            $treeResult['tier_' . $tier++] = array_pop($tree);
        }

        return $treeResult;
    }
}
