<?php

namespace Mageplaza\Affiliate\Helper;

class Calculation extends Data
{
	protected $_address;

	public function collect(
		\Magento\Quote\Model\Quote $quote,
		\Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
		\Magento\Quote\Model\Quote\Address\Total $total
	)
	{
		$this->_address = $shippingAssignment->getShipping()->getAddress();

		return $this;
	}

	public function canCalculate($isDiscount = false)
	{

		// - only calculate discount if has key and do not has first order
		// - comission always calculated if has first order

		$key = $this->getAffiliateKey(); // if no cookie then first order key
		$account = $this->getCurrentAffiliate(); // get aff acc base on current customer id
		$campaigns = [];
		if(!$this->registry->registry('mp_affiliate_account')){
			$this->registry->register('mp_affiliate_account', $this->getAffiliateByKeyOrCode($key));
		}
		$refAcc = $this->registry->registry('mp_affiliate_account');
		if(!$account->getId() && $key){
			if ($refAcc->getId() && $refAcc->isActive()) {
				$campaigns = $this->getAvailableCampaign($refAcc);
			}
		}
		if($isDiscount){
			return count($campaigns) && !$this->hasFirstOrder();
		}

		return count($campaigns);// && $this->hasFirstOrder();
	}

	public function getAvailableCampaign($account = null)
	{
		if(is_null($account)){
			$account = $this->getCurrentAffiliate();
		}

		$cacheKey = 'affiliate_available_campaign_' . $account->getId();
		if (!$this->hasCache($cacheKey)) {
			$campaigns      = $this->campaignFactory->create()->getCollection()
				->getAvailableCampaign($account->getGroupId(), $this->storeManager->getWebsite()->getId());
			$campaignResult = array();
			foreach ($campaigns as $campaign) {
				$campaign->setCommission($this->unserialize($campaign->getCommission()));
				if ($campaign->validate($this->_address)) {
					$campaignResult[] = $campaign;
				}
			}

			$this->saveCache($cacheKey, $campaignResult);
		}

		return $this->getCache($cacheKey);
	}
}
