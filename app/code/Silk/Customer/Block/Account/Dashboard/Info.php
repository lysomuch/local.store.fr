<?php
/**
 * Created by PhpStorm.
 * User: cristen
 * Date: 2018/6/7
 * Time: 9:30
 */

namespace Silk\Customer\Block\Account\Dashboard;


class Info extends \Magento\Customer\Block\Account\Dashboard\Info
{

    /** @var \Magento\Sales\Model\ResourceModel\Order\Collection */
    protected $orderCollection;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param \Magento\Customer\Helper\View $helperView
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Magento\Customer\Helper\View $helperView,
        array $data = [],
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
    ) {
        $this->orderCollection = $orderCollectionFactory->create();
        parent::__construct($context, $currentCustomer, $subscriberFactory, $helperView, $data);
    }

    /**
     * 获取当前客户配送中的订单数量
     * @return int
     */
    public function getShippingNum() {
        //获取当前用户
        $customer_id = $this->getCustomer()->getId();

        //筛选当前用户的订单
        $orderCollection = clone $this->orderCollection;
        $orderCollection->addFieldToFilter('customer_id', $customer_id);
        //订单状态为processing
        $orderCollection->addFieldToFilter('status', 'processing');
        // 获取 count collection
        return $orderCollection->getSize();
    }

    /**
     * 获取当前客户全部订单数量
     * @return int
     */
    public function getHistoryNum() {
        //获取当前用户
        $customer_id = $this->getCustomer()->getId();

        //筛选当前用户的订单
        $orderCollection = clone $this->orderCollection;
        $orderCollection->addFieldToFilter('customer_id', $customer_id);

        // 获取 count collection
        return $orderCollection->getSize();
    }
}