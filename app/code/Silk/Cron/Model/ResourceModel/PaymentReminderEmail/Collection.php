<?php
/**
 * Created by PhpStorm.
 * User: cristen
 * Date: 2018/5/30
 * Time: 20:48
 */

namespace Silk\Cron\Model\ResourceModel\PaymentReminderEmail;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * 定义资源模型
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Silk\Cron\Model\PaymentReminderEmail', 'Silk\Cron\Model\ResourceModel\PaymentReminderEmail');
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

    /**
     * 根据$order_ids列表筛选数据表payment_reminder_email中已存在的order_id列表
     * @param $order_ids array
     */
    public function getExistsOrderData($order_ids) {
        $this->getSelect()
            ->columns(['order_id'])
            ->where('order_id IN(' . implode(',', $order_ids) . ')');
    }
}