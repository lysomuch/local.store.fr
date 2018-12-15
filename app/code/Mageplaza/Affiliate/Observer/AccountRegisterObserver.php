<?php

namespace Mageplaza\Affiliate\Observer;

use Magento\Framework\Event\ObserverInterface;
use Mageplaza\Affiliate\Model\AccountFactory;
use Magento\Customer\Api\AccountManagementInterface;

class AccountRegisterObserver implements ObserverInterface
{
	/**
	 * @var \Mageplaza\Affiliate\Model\AccountFactory
	 */
	protected $_accountFactory;

	/**
	 * @var \Magento\Captcha\Helper\Data
	 */
	protected $_helper;

	/**
	 * @var \Magento\Framework\Message\ManagerInterface
	 */
	protected $messageManager;

	/**
	 * @param \Mageplaza\Affiliate\Helper\Data $helper
	 * @param \Magento\Framework\Message\ManagerInterface $messageManager
	 * @param \Mageplaza\Affiliate\Model\AccountFactory $accountFactory
	 */
	public function __construct(
		\Mageplaza\Affiliate\Helper\Data $helper,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		AccountFactory $accountFactory
	)
	{
		$this->_accountFactory = $accountFactory;
		$this->_helper         = $helper;
		$this->messageManager  = $messageManager;
	}

	/**
	 * Check Captcha On User Login Page
	 *
	 * @param \Magento\Framework\Event\Observer $observer
	 * @return $this
	 */
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$urlReferer = $_SERVER['HTTP_REFERER'];
		if (strpos($urlReferer, 'affiliate')) {
			$controler  = $observer->getEvent()->getAccountController();
			$customer   = $observer->getEvent()->getCustomer();
			$data       = $controler->getRequest()->getPostValue();
			$affAccount = $this->_accountFactory->create();

			$affiliateGroup          = $this->_helper->getAffiliateConfig('account/affiliate_group');
			$data['affiliate_group'] = $affiliateGroup;

			$affAccount->addData($data);

			$affAccount->setCustomerId($customer->getId());
			if ($email = $this->_helper->getCustomerReferBy()) {
				$affAccount->setReferredBy($email);
			}
			try {
				$affAccount->save();
				$this->messageManager->addSuccess(__('The Account has been saved.'));
			} catch (\Magento\Framework\Exception\LocalizedException $e) {
				$this->messageManager->addError($e->getMessage());
			} catch (\RuntimeException $e) {
				$this->messageManager->addError($e->getMessage());
			} catch (\Exception $e) {
				$this->messageManager->addException($e, __('Something went wrong while saving the Account.'));
			}
		}

		return $this;
	}
}
