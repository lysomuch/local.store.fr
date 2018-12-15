<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageplaza\Affiliate\Controller\Account;

use Magento\Framework\DataObject;

class Referemail extends \Mageplaza\Affiliate\Controller\Account
{
	const XML_PATH_REFER_EMAIL_TEMPLATE = 'affiliate/refer/account_sharing';

	/**
	 * Default customer account page
	 *
	 * @return \Magento\Framework\View\Result\Page
	 */
	public function execute()
	{
		$data           = $this->getRequest()->getPostValue();
		$contacts       = $data['contacts'];
		$subject        = $data['subject'];
		$message        = $data['content'];
		$sharingUrl 	= $this->dataHelper->getSharingUrl();
		$contacts = explode(',', $contacts);

		foreach ($contacts as $key => $email) {
			if (strpos($email, '<') !== false) {
				$name  = substr($email, 0, strpos($email, '<'));
				$email = substr($email, strpos($email, '<') + 1);
			} else {
				$emailIdentify = explode('@', $email);
				$name          = $emailIdentify[0];
			}

			$name  = trim($name, '\'"');
			$email = trim(rtrim(trim($email), '>'));
			if (!\Zend_Validate::is($email, 'EmailAddress')) {
				continue;
			}

			$escaper           = $this->_objectManager->create('Magento\Framework\Escaper');
			$params['message'] = $message;
			$params['subject'] = $subject;
			try {
				$this->dataHelper->sendEmailTemplate(
					new DataObject(['name' => $name, 'email' => $email, 'refer_url' => $sharingUrl]),
					self::XML_PATH_REFER_EMAIL_TEMPLATE,
					$params
				);
				$this->messageManager->addSuccess(__('Your email has been sent.'));
			} catch (\Exception $e) {
				$this->messageManager->addError(__('The email cannot be sent'));
			}
		}

		$this->_redirect('*/*/refer');

		return;
	}
}
