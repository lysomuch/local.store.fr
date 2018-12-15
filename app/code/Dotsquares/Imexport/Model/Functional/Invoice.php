<?php
namespace Dotsquares\Imexport\Model\Functional;
class Invoice extends \Magento\Framework\Model\AbstractModel
{
    public function createInvoice($order_id,$invoice_item,$date)
    {	
		$order_data = $this->getOrderModel($order_id);
        $orderid = $order_data->getId();
        $object_manager = \Magento\Framework\App\ObjectManager::getInstance();
        $orderRepository = $object_manager->create('\Magento\Sales\Api\OrderRepositoryInterface');
        $invoiceService = $object_manager->create('\Magento\Sales\Model\Service\InvoiceService');
        $transaction = $object_manager->create('\Magento\Framework\DB\Transaction');
        $invoiceSender = $object_manager->create('\Magento\Sales\Model\Order\Email\Sender\InvoiceSender');
        $order = $orderRepository->get($orderid);
        if(!$order->canInvoice()) {
            $invoice = $invoiceService->prepareInvoice($order);
            $invoice->register();
            $invoice->save();
            $transactionSave = $transaction->addObject(
                $invoice
            )->addObject(
                $invoice->getOrder()
            );
            $transactionSave->save();
            $invoiceSender->send($invoice);
            $order->addStatusHistoryComment(
                __('Notified customer about invoice #%1.', $invoice->getId())
            )
            ->setIsCustomerNotified(true)
            ->save();
            return $invoice->getId();
        }
    }
    
    
    public function getOrderModel($last_order_increment_id)
    {
        $object_manager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $object_manager->create('Magento\Sales\Model\Order')->loadByIncrementId($last_order_increment_id);
        return $order;
    }
}