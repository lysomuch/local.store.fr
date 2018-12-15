<?php
namespace Silk\Kingdee\Api;

interface ProductStockInterface
{
    /**
     * get products stock
     *
     * @api
     * @return mixed
     */
    public function getAllProductStock();
}