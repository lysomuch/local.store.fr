<?php
/**
 * All rights reserved.
 *
 * @authors bob.song (song01140228@163.com)
 * @date    18-5-6 下午5:27
 * @version 0.1.0
 */


namespace Silk\FlashSales\Api;


interface SalesInterface
{
    /**
     * Get relate glasses by product id
     *
     * @api
     * @param string $productId Product Id.
     * @return string Greeting message Product.
     */
    public function exec($productId);
}