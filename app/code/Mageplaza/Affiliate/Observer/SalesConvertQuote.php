<?php

namespace Mageplaza\Affiliate\Observer;

use Magento\Framework\Event\ObserverInterface;
use \Mageplaza\Affiliate\Helper\Data as DataHelper;

class SalesConvertQuote implements ObserverInterface
{
	/**
     * @var \Mageplaza\Affiliate\Helper\Data
     */
    protected $_helper;

    /**
     * SalesConvertQuote constructor.
     * @param \Mageplaza\Affiliate\Helper\Data $helper
     */
    public function __construct(DataHelper $helper)
    {
        $this->_helper = $helper;
    }
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$order = $observer->getEvent()->getOrder();
		$quote = $observer->getEvent()->getQuote();
		if($quote->getAffiliateKey()) {
			$order->setAffiliateKey($quote->getAffiliateKey())
				->setAffiliateCommission($quote->getAffiliateCommission())
				->setTotalAffiliateCommission($quote->getTotalAffiliateCommission())
				->setAffiliateDiscountAmount($quote->getAffiliateDiscountAmount())
				->setBaseAffiliateDiscountAmount($quote->getBaseAffiliateDiscountAmount())
                ->setAffiliateCampaigns($quote->getAffiliateCampaigns());
		}
		$this->_helper->getCheckoutSession()->setAffDiscountData([]);

		return $this;
	}
}
