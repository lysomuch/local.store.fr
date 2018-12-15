<?php
namespace Dotsquares\Imexport\Model;

class Orders extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Dotsquares\Imexport\Model\Resource\Orders');
    }
}