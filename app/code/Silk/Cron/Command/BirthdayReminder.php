<?php
/**
 * 客户生日提醒
 * Created by PhpStorm.
 * User: cristen
 * Date: 2018/5/30
 * Time: 10:36
 */

namespace Silk\Cron\Command;
use Silk\Cron\Model\BirthdayReminderEmailFactory;

class BirthdayReminder
{
    /** @var BirthdayReminderEmailFactory */
    protected $BirthdayReminderEmailFactory;

    public function __construct(
        BirthdayReminderEmailFactory $BirthdayReminderEmailFactory
    )
    {
        $this->BirthdayReminderEmailFactory = $BirthdayReminderEmailFactory;
    }


    //收集符合条件的客户生日到表birthday_reminder_email
    public function updateBirthdayReminder()
    {
        $start_time = microtime(true);
        $BirthdayReminderEmailModel = $this->BirthdayReminderEmailFactory->create();
        $BirthdayReminderEmailModel->updateBirthdayReminder();

        //添加日志记录
        $end_time = microtime(true);
        $log = date('Y-m-d H:i:s') . '，定时收集符合条件的客户生日到表birthday_reminder_email的运行时间为 ' . ($end_time - $start_time) . ' 秒';

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/updateBirthdayReminder.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($log);

        exit($log);
    }

    //发送邮件
    public function sendEmail()
    {
        $start_time = microtime(true);
        $BirthdayReminderEmailModel = $this->BirthdayReminderEmailFactory->create();
        $BirthdayReminderEmailModel->sendEmail();

        //添加日志记录
        $end_time = microtime(true);
        $log = date('Y-m-d H:i:s') . '，定时发送BirthdayReminderEmail运行时间为 ' . ($end_time - $start_time) . ' 秒';

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/sendBirthdayReminderEmail.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($log);

        exit($log);
    }
}