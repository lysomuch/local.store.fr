<?php
namespace Dotsquares\Imexport\Model\Functional;
class Shipment extends \Magento\Framework\Model\AbstractModel
{
    public function createShipment($order_id,$shipped_item,$date)
    {
        $object_manager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $this->getOrderModel($order_id);
        if (!$order->canShip()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('You cant create the Shipment.'));
        }
        $convertOrder = $object_manager->create('Magento\Sales\Model\Convert\Order');
        $shipment = $convertOrder->toShipment($order);
        foreach ($order->getAllItems() AS $orderItem) {
            if (! $orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                continue;
            }
            $qtyShipped = $orderItem->getQtyToShip();
            $shipmentItem = $convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);
            $shipment->addItem($shipmentItem);
        }
        $shipment->register();
        $shipment->getOrder()->setIsInProcess(true);
        try {
            $shipment->save();
            $shipment->getOrder()->save();
            $object_manager->create('Magento\Shipping\Model\ShipmentNotifier')
            ->notify($shipment);
            $shipment->save();
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
    }
    
    public function getOrderModel($last_order_increment_id)
    {
       	$object_manager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $object_manager->create('Magento\Sales\Model\Order')->loadByIncrementId($last_order_increment_id);
        return $order;
    }
}