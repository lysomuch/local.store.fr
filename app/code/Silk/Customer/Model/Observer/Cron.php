<?php
/**
 * All rights reserved.
 *
 * @authors daniel (luo3555@qq.com)
 * @date    18-5-29 下午5:04
 * @version 0.1.0
 *
 * logic:
 * 1. 获取之前12个月的订单
 * 1.1 获取近一年的消费者id
 * 2. 计算每个用户之前12个月的消费额度
 * 3. 根据消费额度自动升/降级用户等级
 *
 * 需要配置，计划任务执行时间，升级，界限
 */
namespace Silk\Customer\Model\Observer;

class Cron
{
    protected $_config;

    /** @var \Magento\Sales\Model\Order */
    protected $_order;

    /** @var  \Zend_Db_Adapter_Abstract */
    protected $_adapt;

    /** @var array  */
    protected $_mapping;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Model\Order $order
    )
    {
        $this->_config = $scopeConfig;
        $this->_order = $order;
    }

    public function assigned()
    {
        $lastCustomerId = 0;
        while (true) {
            // 1. 获取近一年的消费者id 和消费总金额
            $customers = $this->getOrders($lastCustomerId);
            if (empty($customers->getSize())) {
                break;
            }
            // 2. 根据金额设定用户等级
            foreach ($customers as $customer) {
                $groupId = $this->getGroupMapping($customer['amount']);
                $this->_adapt->update(
                    'customer_grid_flat',
                    ['group_id' => $groupId],
                    sprintf('entity_id=%d', $customer['id'])
                );
                $this->_adapt->update(
                    'customer_entity',
                    ['group_id' => $groupId],
                    sprintf('entity_id=%d', $customer['id'])
                );
                $lastCustomerId = $customer->getData('id');
            }
        }
    }

    public function getOrders($lastCustomerId)
    {
        // 1. 获取近一年的消费者id 和消费总金额
        $collection = $this->_order->getCollection()
                        ->addFieldToFilter('customer_id', ['gt' => $lastCustomerId])
                        ->addFieldToFilter('status', $this->_getOrderStatus())
                        ->addFieldToFilter('created_at', ['gt' => $this->getInterval()]);

        $this->_adapt = $collection->getSelect()
            ->group('customer_id')
            ->reset('columns')
            ->columns(new \Zend_Db_Expr('customer_id AS id, sum(grand_total) AS amount'))
            ->order('customer_id asc')
            ->limit(100)
            ->getAdapter();

        return $collection;
    }

    protected function _getOrderStatus()
    {
        return \Magento\Sales\Model\Order::STATE_COMPLETE;
    }

    public function getGroupMapping($amount=null)
    {
        if (is_null($this->_mapping)) {
            $value = json_decode($this->_config->getValue('customer/auto_group/custom_attributes'), true);
            foreach ($value as $item) {
                $this->_mapping[$item['group']] = $item['amount'];
            }
        }

        $group = null;
        foreach ($this->_mapping as $_group => $price)
        {
            if ($price > $amount) {
                break;
            }
            $group = $_group;
        }
        return $group;
    }

    public function getInterval()
    {
        return date('Y-m-d H:i:s', strtotime('-1 Year'));
    }
}