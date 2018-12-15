<?php
/**
 * Created by PhpStorm.
 * User: cristen
 * Date: 2018/5/30
 * Time: 20:48
 */

namespace Silk\Cron\Model\ResourceModel\OrderReviewEmail;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * 定义资源模型
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Silk\Cron\Model\OrderReviewEmail', 'Silk\Cron\Model\ResourceModel\OrderReviewEmail');
    }

    public function getOrderData() {
        $this->getSelect()
             ->columns(['s.customer_email', 's.customer_firstname', 's.customer_lastname'])
             ->joinLeft(
                 ['s' => $this->getTable('sales_order')],
                 'main_table.order_id = s.entity_id',
                 []
             )
             ->where('main_table.status=0');
    }
}