<?php
/**
 * Created by PhpStorm.
 * User: cristen
 * Date: 2018/5/30
 * Time: 20:48
 */

namespace Silk\Cron\Model\ResourceModel\AbandonedCartEmail;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * 定义资源模型
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Silk\Cron\Model\AbandonedCartEmail', 'Silk\Cron\Model\ResourceModel\AbandonedCartEmail');
    }

    public function getCartData() {
        $this->getSelect()
             ->columns(['q.customer_id', 'q.customer_email', 'q.customer_firstname', 'q.customer_lastname'])
             ->joinLeft(
                 ['q' => $this->getTable('quote')],
                 'main_table.quote_id = q.entity_id',
                 []
             )
             ->where('main_table.status=0 AND q.items_count>0');
    }
}