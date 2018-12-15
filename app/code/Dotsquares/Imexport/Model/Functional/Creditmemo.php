<?php
namespace Dotsquares\Imexport\Model\Functional;
class Creditmemo extends \Magento\Framework\model\AbstractModel
{
    public function createCreditMemo($order_id,$credit_item,$creditDetail)
    {
        $order_data = $this->getOrderModel($order_id);
        $orderid = $order_data->getId();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->create('\Magento\Sales\Model\Order');
        $creditmemoService = $objectManager->create('\Magento\Sales\Model\Service\CreditmemoService');
        $invoice = $objectManager->create('\Magento\Sales\Model\Order\Invoice');
        $creditmemoFactory = $objectManager->create('\Magento\Sales\Model\Order\CreditmemoFactory');
        $order->load($orderid);
        $invoices = $order->getInvoiceCollection();
        foreach ($invoices as $invoice) {
            $invoiceincrementid = $invoice->getIncrementId();
        }
        $invoiceobj = $invoice->loadByIncrementId($invoiceincrementid);
        $creditmemo = $creditmemoFactory->createByOrder($order);
        $creditmemo->setInvoice($invoiceobj);
        $creditmemoService->refund($creditmemo);
        return true;
    }
    
    public function getOrderModel($last_order_increment_id)
    {
        $object_manager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $object_manager->create('Magento\Sales\Model\Order')->loadByIncrementId($last_order_increment_id);
		return $order;
    }
}