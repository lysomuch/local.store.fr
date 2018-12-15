<?php
/**
 * Created by PhpStorm.
 * User: cristen
 * Date: 2018/5/30
 * Time: 18:30
 */

namespace Silk\Cron\Model;


use Magento\Setup\Exception;

class BirthdayReminderEmail extends \Magento\Framework\Model\AbstractModel
{
    /** @var \Magento\Customer\Model\ResourceModel\Customer\Collection */
    protected $customerCollection;

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
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->customerCollection = $customerCollectionFactory->create();
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
        $this->_init('Silk\Cron\Model\ResourceModel\BirthdayReminderEmail');
    }

    //将满足提醒条件的客户数据添加到表birthday_reminder_email中
    public function updateBirthdayReminder()
    {
        //获取满足条件的数据
        $data_list = $this->_getBirthdayReminder();

        //将数据添加到表birthday_reminder_email
        $result = $this->_saveBirthdayReminder($data_list);

        if($result['code'] != 200) { //log exception
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/BirthdayReminderException.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info(__METHOD__ . '【' . $result['msg'] . '】');
        }
    }

    //向客户发生日提醒邮件
    public function sendEmail()
    {
        if( ! $this->_getConfig('enabled')) { //是否启用发邮件功能
            return false;
        }

        //获取status为0的记录
        $customerCollection = $this->getCollection();
        $customerCollection->getCustomerData();
        $customer_list = $customerCollection->load();

        foreach($customer_list as $item) {
            //发邮件
            $customer_name = $item->getdata('firstname') . ' ' . $item->getdata('lastname');
            $this->send($item->getdata('email'), $customer_name);

            //更新状态为已发送
            $item->setData('status', 2);
            $item->save();
        }

        return true;
    }

    /**
     * 获取满足生日提醒的客户数据
     * @return array
     */
    private function _getBirthdayReminder()
    {
        //获取触发生日提醒的日期
        $date = $this->_getBirthdayDate();

        //获取用户组
        $group_id = $this->_getConfig('group_id');
        if( ! empty($group_id)) {
            $this->customerCollection->addFieldToFilter('group_id', $group_id);
        }

        $this->customerCollection->addFieldToFilter('dob', ['like' => '%-' . $date]);

        // 获取 collection
        $customer_list = $this->customerCollection->load();

        $list = [];
        foreach($customer_list as $item) {
            $list[] = [
                'customer_id' => $item->getdata('entity_id')
            ];
        }

        return $list;
    }

    /**
     * 批量保存数据到表birthday_reminder_email
     * @param array $data_list
     * @return array
     */
    private function _saveBirthdayReminder($data_list=[])
    {
        if( ! $data_list) return ['code'=>200, 'msg'=>'no data to save.'];

        try {
            $tableName = $this->resource->getTableName('birthday_reminder_email');
            $num = $this->connection->insertMultiple($tableName, $data_list);
            return ['code'=>200, 'msg'=> "saved {$num} records"];
        } catch (\Exception $e) {
            return ['code'=>0, 'msg'=>$e->getMessage()];
        }
    }

    /**
     * 获取触发生日提醒的日期
     * @return Date
     */
    private function _getBirthdayDate() {
        $cur_timestamp = $this->date->gmtTimestamp();
        //提前几天提醒？
        $days = $this->_getConfig('days_in_advance');
        $timestamp = strtotime("+{$days} day", $cur_timestamp);
        $date = $this->date->gmtDate('m-d', $timestamp);

        return $date;
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
                    'store' => $this->_storeManager->getStore()
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
            'sales_email/birthday_reminder_email/' . $field,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}