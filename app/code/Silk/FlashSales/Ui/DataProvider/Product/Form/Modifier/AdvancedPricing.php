<?php
/**
 * All rights reserved.
 *
 * @authors bob.song (song01140228@163.com)
 * @date    18-5-6 下午5:27
 * @version 0.1.0
 */


namespace Silk\FlashSales\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AdvancedPricing As MageAdvancedPricing;

class AdvancedPricing extends MageAdvancedPricing
{
    public function modifyMeta(array $meta)
    {
        return parent::modifyMeta($meta);
    }
}