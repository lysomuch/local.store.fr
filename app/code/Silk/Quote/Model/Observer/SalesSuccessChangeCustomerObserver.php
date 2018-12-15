<?php

/**
 * User: Bob song <song01140228@163.com>
 * @date 18-5-21 下午6:51
 */


namespace Silk\Quote\Model\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SalesSuccessChangeCustomerObserver implements  ObserverInterface
{

    protected $_customer;

    /**
     * SalesSuccessChangeCustomerObserver constructor.
     * @param \Magento\Customer\Model\Customer $customer
     */
    public function __construct(
        \Magento\Customer\Model\Customer $customer
    )
    {
        $this->_customer = $customer;
    }


    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getOrder();
        $customer = $this->_customer->load($order->getCustomerId());
        if ($customer->getIsNewCustomer()) {
            $customer->setIsNewCustomer(0);
            $customer->save();
        }
    }
}