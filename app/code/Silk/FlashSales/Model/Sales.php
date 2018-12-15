<?php
/**
 * All rights reserved.
 *
 * @authors bob.song (song01140228@163.com)
 * @date    18-5-6 下午5:27
 * @version 0.1.0
 */


namespace Silk\FlashSales\Model;

use Silk\FlashSales\Api\SalesInterface;

class Sales implements SalesInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Directory\Model\PriceCurrency
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Framework\Json\Encoder
     */
    protected $encoder;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Silk\Catalog\Helper\SpecialPrice
     */
    protected $specialPriceHelper;

    /**
     * Sales constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Directory\Model\PriceCurrency $priceCurrency
     * @param \Magento\Framework\Json\Encoder $encoder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Silk\Catalog\Helper\SpecialPrice $specialPriceHelper
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Directory\Model\PriceCurrency $priceCurrency,
        \Magento\Framework\Json\Encoder $encoder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Silk\Catalog\Helper\SpecialPrice $specialPriceHelper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
    )
    {
        $this->objectManager = $objectManager;
        $this->priceCurrency = $priceCurrency;
        $this->encoder = $encoder;
        $this->localeDate = $localeDate;
        $this->storeManager = $storeManager;
        $this->specialPriceHelper = $specialPriceHelper;
    }

    public function exec($productId)
    {
        $result = ['status' => false ];
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->objectManager->create('\Magento\Catalog\Model\Product');
        $product = $product->load($productId);

        if ($product->getId() && $product->getSpecialToDate() != null) {
            $notExpire = $this->specialPriceHelper->isScopeDateInInterval(
                $this->storeManager->getStore()->getId(),
                $product->getSpecialFromDate(),
                $product->getSpecialToDate()
            );
            if ($notExpire) {
                $regularPrice = $product->getPriceInfo()->getPrice('regular_price')->getValue();
                $finalPrice = $product->getPriceInfo()->getPrice('final_price')->getValue();
                $save = $regularPrice - $finalPrice;
                $scale = ($save/$regularPrice)*100;

                $showFlashSaleIcon = $product->getIsFlashSale()? true: false;

                $result = [
                    'status' => $showFlashSaleIcon,
                    'end_date' => $product->getSpecialToDate(),
                    'scale' => (int)$scale,
                    'save' => $this->priceCurrency->convertAndFormat($save, false),
                    'price' => $this->priceCurrency->format($finalPrice)
                ];
            }
        }

        $result['gtm_data'] = [
            'event' => 'Ecommerce Event',
            'ecommerceAction' => 'Add To Cart',
            'ecommerce' => [
                'currencyCode' => $this->priceCurrency->getCurrency()->getCurrencyCode(),
                'add' => [
                    'products' => [
                        [
                            'name' => $product->getName(),
                            'id' => $product->getSku(),
                            'price' => $product->getPrice(),
                            'brand' => $product->getAttributeText('brand'),
                            'quanlity' => 1
                        ]
                    ]
                ]
            ]
        ];

        return $this->encoder->encode($result);
    }

    /**
     * @return bool
     */
    protected function isScopeDateInInterval()
    {
        return $this->localeDate->isScopeDateInInterval(
            $this->product->getStore(),
            $this->getSpecialFromDate(),
            $this->getSpecialToDate()
        );
    }
}