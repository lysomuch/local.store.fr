<?php
/**
 * Created by PhpStorm.
 * User: cristen
 * Date: 2018/5/30
 * Time: 18:30
 */

namespace Silk\Cron\Model;


use Magento\Setup\Exception;

class AbandonedCartEmail extends \Magento\Framework\Model\AbstractModel
{
    /** @var \Magento\Quote\Model\ResourceModel\Quote\Collection */
    protected $quoteCollection;

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
        \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->quoteCollection = $quoteCollectionFactory->create();
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
        $this->_init('Silk\Cron\Model\ResourceModel\AbandonedCartEmail');
    }

    //将满足条件的废弃购物车数据添加到表abandoned_cart_email中
    public function updateAbandonedCart()
    {
        //获取满足条件的购物车数据
        $data_list = $this->_getAbandonedCart();

        //将购物车数据添加到表abandoned_cart_email
        $result = $this->_saveAbandonedCart($data_list);

        if($result['code'] != 200) { //log exception
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/AbandonedCartException.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info(__METHOD__ . '【' . $result['msg'] . '】');
        }
    }

    //向客户发提醒“清空”购物车的邮件
    public function sendEmail()
    {
        if( ! $this->_getConfig('enabled')) { //是否启用发邮件功能
            return false;
        }

        //获取status为0的记录
        $cartCollection = $this->getCollection();
        $cartCollection->getCartData();
        $quote_list = $cartCollection->load();

        foreach($quote_list as $item) {
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
     * 获取满足条件的购物车数据
     * @return array
     */
    private function _getAbandonedCart()
    {
        //筛选购物车的起始更新时间范围
        $arr_time = $this->_getTimestampRange();
        $this->quoteCollection->addFieldToFilter('updated_at', ['from' => $arr_time[0], 'to' => $arr_time[1]]);
        //没有清空或下单
        $this->quoteCollection->addFieldToFilter('is_active', 1);
        //购物车有商品
        $this->quoteCollection->addFieldToFilter('items_count', ['gt' => 0]);
        //能获得客户email
        $this->quoteCollection->addFieldToFilter('customer_email', ['notnull' => 'customer_email']);
        // 获取 collection
        $quote_list = $this->quoteCollection->load();

        $list = [];
        foreach($quote_list as $item) {
            $list[] = [
                'quote_id' => $item->getdata('entity_id'),
                'customer_id' => $item->getdata('customer_id')
            ];
        }

        return $list;
    }

    /**
     * 批量保存数据到表abandoned_cart_email
     * @param array $data_list
     * @return array
     */
    private function _saveAbandonedCart($data_list=[])
    {
        if( ! $data_list) return ['code'=>200, 'msg'=>'no data to save.'];

        try {
            $tableName = $this->resource->getTableName('abandoned_cart_email');
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
        //客户将商品放在购物车没有清空或下单的持续（间隔）天数
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
                    'checkout_url' => $base_url . '/checkout/cart'
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
            'sales_email/abandoned_cart_email/' . $field,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}