<?php
/**
 * 客户下单两周后，邮件邀请评论
 * Created by PhpStorm.
 * User: cristen
 * Date: 2018/5/30
 * Time: 10:36
 */

namespace Silk\Cron\Command;
use Silk\Cron\Model\OrderReviewEmailFactory;

class OrderReview
{
    /** @var OrderReviewEmailFactory */
    protected $OrderReviewEmailFactory;

    public function __construct(
        OrderReviewEmailFactory $OrderReviewEmailFactory
    )
    {
        $this->OrderReviewEmailFactory = $OrderReviewEmailFactory;
    }


    //收集符合条件的客户订单到表order_review_email
    public function updateOrderReview()
    {
        $start_time = microtime(true);
        $OrderReviewEmailModel = $this->OrderReviewEmailFactory->create();
        $OrderReviewEmailModel->updateOrderReview();

        //添加日志记录
        $end_time = microtime(true);
        $log = date('Y-m-d H:i:s') . '，定时收集符合条件的客户订单到表order_review_email的运行时间为 ' . ($end_time - $start_time) . ' 秒';

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/updateOrderReview.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($log);

        exit($log);
    }

    //发送邮件
    public function sendEmail()
    {
        $start_time = microtime(true);
        $OrderReviewEmailModel = $this->OrderReviewEmailFactory->create();
        $OrderReviewEmailModel->sendEmail();

        //添加日志记录
        $end_time = microtime(true);
        $log = date('Y-m-d H:i:s') . '，定时发送OrderReviewEmail运行时间为 ' . ($end_time - $start_time) . ' 秒';

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/sendOrderReviewEmail.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($log);

        exit($log);
    }
}