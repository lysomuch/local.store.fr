<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Silk\SalesSequence\Model;

use Magento\Framework\App\ResourceConnection as AppResource;
use Magento\Framework\DB\Sequence\SequenceInterface;
use Magento\SalesSequence\Model\Meta;
use Magento\Setup\Exception;

/**
 * Class Sequence represents sequence in logic
 *
 * @api
 * @since 100.0.2
 */
class Sequence extends \Magento\SalesSequence\Model\Sequence
{
    /**
     * Default pattern for Sequence
     */
    const DEFAULT_PATTERN  = "%s%'.09d%s";

    /**
     * @var string
     */
    private $lastIncrementId;

    /**
     * @var Meta
     */
    private $meta;

    /**
     * @var false|\Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;

    /**
     * @var string
     */
    private $pattern;

    /** @var \Magento\Framework\App\ResourceConnection */
    protected $resource;

    /** @var \Magento\Framework\Stdlib\DateTime\DateTime */
    protected $date;

    /** @var \Magento\Framework\Stdlib\DateTime\Timezone */
    protected $timezone;

    /** current timestamp in config timezone */
    private $currentTimestamp;

    /** meta entity type */
    private $type;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var orderInterface
     */
    protected $orderInterface;

    /**
     * @var storeManager
     */
    protected $storeManager;

    /**
     * @param Meta $meta
     * @param AppResource $resource
     * @param string $pattern
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        Meta $meta,
        AppResource $resource,
        $pattern = self::DEFAULT_PATTERN
    ) {
        $this->meta = $meta;
        $this->connection = $resource->getConnection('sales');
        $this->pattern = $pattern;
        $this->resource = $resource;
        $this->type = $meta->getEntityType();

        /** @var \Magento\Framework\Stdlib\DateTime\DateTime $coreDate */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->date = $objectManager->get(\Magento\Framework\Stdlib\DateTime\DateTime::class);
        $this->timezone = $objectManager->get(\Magento\Framework\Stdlib\DateTime\Timezone::class);
        $this->currentTimestamp = strtotime($this->timezone->formatDatetime($this->date->gmtDate()));
        $this->orderInterface = $objectManager->create('Magento\Sales\Api\Data\OrderInterface');
        $this->storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface'); 

        $this->registry = $registry;
    }

    /**
     * Retrieve current value
     *
     * @return string
     * @throws
     */
    public function getCurrentValue()
    {
        $cur_date = $this->date->gmtDate('Y-m-d', $this->currentTimestamp);
        $this->connection->beginTransaction();
        try {

            //获取当天最新的流水号serial_number
            $tableName = $this->resource->getTableName($this->meta->getSequenceTable());
            $sql = "SELECT serial_number FROM {$tableName} WHERE `date`='{$cur_date}' ORDER BY sequence_value DESC LIMIT 1 FOR UPDATE";
            $rst = $this->connection->fetchOne($sql);
            $cur_value = $rst ? $rst : 0;
            $next_value = $cur_value + 1;

            $this->connection->insert($tableName, [
                'date' => $cur_date,
                'serial_number' => $next_value
            ]);

            $this->connection->commit();

            $pad_length = $this->type == 'rma_item' ? 4 : 5;
            return str_pad($next_value, $pad_length, "0", STR_PAD_LEFT);
        } catch (\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }

    /**
     * Retrieve next value
     *
     * @return string
     * @throws
     */
    public function getNextValue()
    {
        try {
            $result = '';
            $prefix = $this->storeManager->getStore()->getName().'';
            /*
            if ('invoice' == $this->type) {
                //创建发票
                $currentInvoice = $this->registry->registry('current_invoice');
                if ($currentInvoice) {
                    $result = $this->orderInterface->load($currentInvoice->getOrderId())->getIncrementId();
                }
            } elseif ('shipment' == $this->type) {
                //发货
                $currentShipment = $this->registry->registry('current_shipment');
                if ($currentShipment) {
                    $result = $this->orderInterface->load($currentShipment->getOrderId())->getIncrementId();
                }
            } else
            */
            if ('rma_item' == $this->type) {
                //退货
                $prefix = 'R' . $prefix;
                $result = $prefix . $this->date->gmtDate('ymd', $this->currentTimestamp) . $this->getCurrentValue();
            } else {
                //其他
                $result = $prefix . $this->date->gmtDate('ymd', $this->currentTimestamp) . $this->getCurrentValue();
            }
            return $result;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
