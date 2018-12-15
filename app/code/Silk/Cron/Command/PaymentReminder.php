<?php
/**
 * 客户下单两周后，邮件邀请评论
 * Created by PhpStorm.
 * User: cristen
 * Date: 2018/5/30
 * Time: 10:36
 */

namespace Silk\Cron\Command;
use Silk\Cron\Model\PaymentReminderEmailFactory;

class PaymentReminder
{
    /** @var PaymentReminderEmailFactory */
    protected $PaymentReminderEmailFactory;

    public function __construct(
        PaymentReminderEmailFactory $PaymentReminderEmailFactory
    )
    {
        $this->PaymentReminderEmailFactory = $PaymentReminderEmailFactory;
    }


    //收集符合条件的客户未付款订单到表payment_reminder_email
    public function updatePaymentReminder()
    {
        $start_time = microtime(true);
        $PaymentReminderEmailModel = $this->PaymentReminderEmailFactory->create();
        $PaymentReminderEmailModel->updatePaymentReminder();

        //添加日志记录
        $end_time = microtime(true);
        $log = date('Y-m-d H:i:s') . '，定时收集符合条件的客户未付款订单到表payment_reminder_email的运行时间为 ' . ($end_time - $start_time) . ' 秒';

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/updatePaymentReminder.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($log);

        exit($log);
    }

    //发送邮件
    public function sendEmail()
    {
        $start_time = microtime(true);
        $PaymentReminderEmailModel = $this->PaymentReminderEmailFactory->create();
        $PaymentReminderEmailModel->sendEmail();

        //添加日志记录
        $end_time = microtime(true);
        $log = date('Y-m-d H:i:s') . '，定时发送PaymentReminderEmail运行时间为 ' . ($end_time - $start_time) . ' 秒';

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/sendPaymentReminderEmail.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($log);

        exit($log);
    }
}