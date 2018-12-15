<?php
/**
 * Created by PhpStorm.
 * User: cristen
 * Date: 2018/5/30
 * Time: 18:30
 */

namespace Silk\Cron\Model\ResourceModel;


class BirthdayReminderEmail extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * 初始化资源模型
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('birthday_reminder_email', 'id');
    }
}