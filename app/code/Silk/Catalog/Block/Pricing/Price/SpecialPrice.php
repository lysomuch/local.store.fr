<?php

/**
 * User: Bob song <song01140228@163.com>
 * @date 18-9-17 下午3:25
 */


namespace Silk\Catalog\Block\Pricing\Price;

use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\Adjustment\CalculatorInterface;
use Magento\Framework\Pricing\Price\AbstractPrice;
use Magento\Framework\Pricing\Price\BasePriceProviderInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class SpecialPrice extends \Magento\Catalog\Pricing\Price\SpecialPrice
{
    /**
     * @var \Silk\Catalog\Helper\SpecialPrice
     */
    protected $specialPriceHelper;

    public function __construct(
        Product $saleableItem,
        $quantity,
        CalculatorInterface $calculator,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Silk\Catalog\Helper\SpecialPrice $specialPriceHelper,
        TimezoneInterface $localeDate
    )
    {
        $this->specialPriceHelper = $specialPriceHelper;
        parent::__construct($saleableItem, $quantity, $calculator, $priceCurrency, $localeDate);
    }

    /**
     * @return bool
     */
    public function isScopeDateInInterval()
    {
        $scope = $this->product->getStore();
        return $this->specialPriceHelper->isScopeDateInInterval($scope, $this->getSpecialFromDate(), $this->getSpecialToDate());
    }
}