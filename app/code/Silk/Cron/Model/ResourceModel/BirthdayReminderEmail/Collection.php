<?php
/**
 * Created by PhpStorm.
 * User: cristen
 * Date: 2018/5/30
 * Time: 20:48
 */

namespace Silk\Cron\Model\ResourceModel\BirthdayReminderEmail;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * 定义资源模型
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Silk\Cron\Model\BirthdayReminderEmail', 'Silk\Cron\Model\ResourceModel\BirthdayReminderEmail');
    }

    public function getCustomerData() {
        $this->getSelect()
             ->columns(['c.email', 'c.dob', 'c.firstname', 'c.lastname', 'c.gender'])
             ->joinLeft(
                 ['c' => $this->getTable('customer_entity')],
                 'main_table.customer_id = c.entity_id',
                 []
             )
             ->where('main_table.status=0');
    }
}