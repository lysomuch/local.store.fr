<?php
/**
 * 当登录用户添加商品到购物车却没有下单时，系统会定期给客户发送邮件，提醒客户付款，
 * 避免遗忘在“购物车”里的订单
 * Created by PhpStorm.
 * User: cristen
 * Date: 2018/5/30
 * Time: 10:36
 */

namespace Silk\Cron\Command;
use Silk\Cron\Model\AbandonedCartEmailFactory;

class AbandonedCart
{
    /** @var AbandonedCartEmailFactory */
    protected $abandonedCartEmailFactory;

    public function __construct(
        AbandonedCartEmailFactory $abandonedCartEmailFactory
    )
    {
        $this->abandonedCartEmailFactory = $abandonedCartEmailFactory;
    }


    //收集符合条件的废弃购物车到表abandoned_cart_email
    public function updateAbandonedCart()
    {
        $start_time = microtime(true);
        $abandonedCartEmailModel = $this->abandonedCartEmailFactory->create();
        $abandonedCartEmailModel->updateAbandonedCart();

        //添加日志记录
        $end_time = microtime(true);
        $log = date('Y-m-d H:i:s') . '，定时收集符合条件的废弃购物车到表abandoned_cart_email的运行时间为 ' . ($end_time - $start_time) . ' 秒';

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/updateAbandonedCart.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($log);

        exit($log);
    }

    //发送邮件
    public function sendEmail()
    {
        $start_time = microtime(true);
        $abandonedCartEmailModel = $this->abandonedCartEmailFactory->create();
        $abandonedCartEmailModel->sendEmail();

        //添加日志记录
        $end_time = microtime(true);
        $log = date('Y-m-d H:i:s') . '，定时发送AbandonedCartEmail运行时间为 ' . ($end_time - $start_time) . ' 秒';

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/sendAbandonedCartEmail.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($log);

        exit($log);
    }
}