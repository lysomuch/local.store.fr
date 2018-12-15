<?php
namespace Silk\Sales\Model\Order;

/**
 * User: Bob song <song01140228@163.com>
 * @date 18-8-29 下午12:20
 */
class ShipmentRepository extends \Magento\Sales\Model\Order\ShipmentRepository
{
    /**
     * Performs persist operations for a specified shipment.
     *
     * @param \Magento\Sales\Api\Data\ShipmentInterface $entity
     * @return \Magento\Sales\Api\Data\ShipmentInterface
     * @throws CouldNotSaveException
     */
    public function save(\Magento\Sales\Api\Data\ShipmentInterface $entity)
    {

        \Magento\Framework\App\ObjectManager::getInstance()->get('\Psr\Log\LoggerInterface')
            ->addCritical('Shipment Data', ['data' => $entity]);

        try {
            $this->metadata->getMapper()->save($entity);
            $this->registry[$entity->getEntityId()] = $entity;
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save shipment'), $e);
        }

        return $this->registry[$entity->getEntityId()];
    }
}