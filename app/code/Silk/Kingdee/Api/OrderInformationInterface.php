<?php
namespace Silk\Kingdee\Api;

interface OrderInformationInterface
{
    /**
     * get orders information
     *
     * @api
     * @return mixed
     */
    public function getOrder();

    /**
     * get orders information by date
     *
     * @param string $start
     * @param string $end
     * @return mixed
     */
    public function getOrderByDate($start, $end);
}