<?php
namespace Silk\Gift\Model;


class Product extends \Magento\Framework\Model\AbstractModel
{
	protected function _construct()
	{
		$this->_init('Silk\Gift\Model\ResourceModel\Product');
	}
}