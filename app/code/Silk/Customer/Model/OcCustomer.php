<?php
namespace Silk\Customer\Model;


class OcCustomer extends \Magento\Framework\Model\AbstractModel
{
	protected function _construct()
	{
		$this->_init('Silk\Customer\Model\ResourceModel\OcCustomer');
	}
}