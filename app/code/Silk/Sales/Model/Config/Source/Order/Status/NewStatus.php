<?php
/**
 * Created by PhpStorm.
 * User: cristen
 * Date: 2018/6/20
 * Time: 16:23
 */

namespace Silk\Sales\Model\Config\Source\Order\Status;


class NewStatus extends \Magento\Sales\Model\Config\Source\Order\Status\NewStatus
{
    /**
     * @var string
     */
    protected $_stateStatuses = [
        \Magento\Sales\Model\Order::STATE_NEW,
        'Non-payment',
    ];
}