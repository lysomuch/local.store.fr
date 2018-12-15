<?php

/**
 * User: Bob song <song01140228@163.com>
 * @date 18-8-27 下午5:08
 */


namespace Silk\Sales\Model\Order\Shipment;


class Track extends \Magento\Sales\Model\Order\Shipment\Track
{
    public function afterSave()
    {
        $order = $this->getShipment()->getOrder();

        $shipment =  $this->getShipment();
        $itemDatas = [];
        foreach ($shipment->getAllItems() as $item) {
            $itemDatas[] = $item->getData();
        }

        \Magento\Framework\App\ObjectManager::getInstance()->get('\Psr\Log\LoggerInterface')
            ->addCritical('Shipment Track', [
                'orderId' => $order->getId(),
                'status' => $order->getStatus(),
                'state' => $order->getState(),
                'canInvoice' => $order->canInvoice(),
                'track' => $this->getData(),
                'shipment_data' => $shipment->getData(),
                'shipment_item' => $itemDatas,
                ]);

        if ($order->getStatus() == 'pending' && $order->getState() == 'new' && $order->canInvoice()) {
            $order->setStatus('processing')->setState('processing')->save();
        }

        if ($order->getStatus() == 'processing' && $order->getState() == 'processing' && !$order->canInvoice()) {
            $order->setStatus('complete')->setState('complete')->save();
        }

        return parent::afterSave();
    }
}