<?php
/**
 * Created by PhpStorm.
 * User: cristen
 * Date: 2018/5/30
 * Time: 18:30
 */

namespace Silk\Cron\Model;


use Magento\Setup\Exception;

class OrderReviewEmail extends \Magento\Framework\Model\AbstractModel
{
    /** @var \Magento\Sales\Model\ResourceModel\Order\Collection */
    protected $orderCollection;

    /** @var \Magento\Framework\Stdlib\DateTime\DateTime */
    protected $date;

    /** @var  */
    protected $connection;

    /** @var \Magento\Framework\App\ResourceConnection */
    protected $resource;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->orderCollection = $orderCollectionFactory->create();
        $this->date = $date;
        $this->connection = $resource->getConnection();
        $this->resource = $resource;
        $this->_transportBuilder = $transportBuilder;
        $this->_storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;

        parent::__construct($context, $registry);
    }

    protected function _construct()
    {
        $this->_init('Silk\Cron\Model\ResourceModel\OrderReviewEmail');
    }

    //将满足条件的客户订单数据添加到表order_review_email中
    public function updateOrderReview()
    {
        //获取满足条件的数据
        $data_list = $this->_getOrderReview();

        //将购物车数据添加到表order_review_email
        $result = $this->_saveOrderReview($data_list);

        if($result['code'] != 200) { //log exception
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/OrderReviewException.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info(__METHOD__ . '【' . $result['msg'] . '】');
        }
    }

    //向客户发邀请评论的邮件
    public function sendEmail()
    {
        if( ! $this->_getConfig('enabled')) { //是否启用发邮件功能
            return false;
        }

        //获取status为0的记录
        $orderCollection = $this->getCollection();
        $orderCollection->getOrderData();
        $order_list = $orderCollection->load();

        foreach($order_list as $item) {
            //发邮件
            $customer_name = $item->getdata('customer_firstname') . ' ' . $item->getdata('customer_lastname');
            $this->send($item->getdata('customer_email'), $customer_name);

            //更新状态为已发送
            $item->setData('status', 2);
            $item->save();
        }

        return true;
    }

    /**
     * 获取满足条件的客户订单数据
     * @return array
     */
    private function _getOrderReview()
    {
        //筛选客户订单的起始下单时间范围
        $arr_time = $this->_getTimestampRange();
        $this->orderCollection->addFieldToFilter('created_at', ['from' => $arr_time[0], 'to' => $arr_time[1]]);
        //订单为已完成
        $this->orderCollection->addFieldToFilter('status', 'complete');
        //能获得客户email
        $this->orderCollection->addFieldToFilter('customer_email', ['notnull' => 'customer_email']);
        // 获取 collection
        $order_list = $this->orderCollection->load();

        $list = [];
        foreach($order_list as $item) {
            $list[] = [
                'order_id' => $item->getdata('entity_id'),
                'customer_id' => $item->getdata('customer_id')
            ];
        }

        return $list;
    }

    /**
     * 批量保存数据到表order_review_email
     * @param array $data_list
     * @return array
     */
    private function _saveOrderReview($data_list=[])
    {
        if( ! $data_list) return ['code'=>200, 'msg'=>'no data to save.'];

        try {
            $tableName = $this->resource->getTableName('order_review_email');
            $num = $this->connection->insertMultiple($tableName, $data_list);
            return ['code'=>200, 'msg'=> "saved {$num} records"];
        } catch (\Exception $e) {
            return ['code'=>0, 'msg'=>$e->getMessage()];
        }
    }

    /**
     * 获取起始时间范围
     * @return array  array(startTime, endTime)
     */
    private function _getTimestampRange() {
        $cur_timestamp = $this->date->gmtTimestamp();
        //下单多少天后发邮件提醒
        $days = $this->_getConfig('interval_days');
        $timestamp = strtotime("-{$days} day", $cur_timestamp);
        $date = $this->date->gmtDate('Y-m-d', $timestamp);

        return [
            $date . ' 00:00:00',
            $date . ' 23:59:59',
        ];
    }

    public function send($receiver_email, $receiver_name)
    {
        $base_url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);

        $this->inlineTranslation->suspend();
        $store = $this->_storeManager->getStore()->getId();
        $transport = $this->_transportBuilder->setTemplateIdentifier($this->_getConfig('template'))
            ->setTemplateOptions(['area' => 'frontend', 'store' => $store])
            ->setTemplateVars(
                [
                    'customer_name' => $receiver_name,
                    'store' => $this->_storeManager->getStore(),
                ]
            )
            ->setFrom($this->_getConfig('identity'))
            ->addTo($receiver_email, $receiver_name)
            ->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();
        return $this;
    }

    //获取系统后台配置
    protected function _getConfig($field) {
        return $this->scopeConfig->getValue(
            'sales_email/order_review_email/' . $field,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}