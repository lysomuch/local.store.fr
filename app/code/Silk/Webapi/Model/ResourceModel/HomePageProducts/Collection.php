<?php
namespace Silk\Webapi\Model\ResourceModel\HomePageProducts;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * 定义资源模型
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Silk\Webapi\Model\HomePageProducts', 'Silk\Webapi\Model\ResourceModel\HomePageProducts');
    }

    
}