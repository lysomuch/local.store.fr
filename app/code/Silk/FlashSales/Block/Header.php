<?php
/**
 * Created by PhpStorm.
 * User: bob
 * Date: 18-5-8
 * Time: 下午4:13
 */

namespace Silk\FlashSales\Block;


class Header extends \Magento\Catalog\Block\Product\View
{
    /** @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface */
    protected $date;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        array $data = []
    )
    {
        $this->date =  $date;
        parent::__construct($context, $urlEncoder, $jsonEncoder, $string, $productHelper, $productTypeConfig, $localeFormat, $customerSession, $productRepository, $priceCurrency, $data);
    }

    /**
     * @return int|mixed
     */
    public function getEndTime()
    {
        $product = $this->getProduct();
        if ($product->getIsFlashSales()) {
            $endFlashSalesTime = $this->date->date(new \DateTime($product->getSpecialToDate()))->format('Y-m-d H:i:s');
            if ($endFlashSalesTime) {
                return $endFlashSalesTime;
            }
        }
        return 0;
    }

    /**
     * @return int|mixed
     */
    public function getStartTime()
    {
        $product = $this->getProduct();
        if ($product->getIsFlashSales()) {
            $endFlashSalesTime = $this->date->date(new \DateTime($product->getSpecialFromDate()))->format('Y-m-d H:i:s');
            if ($endFlashSalesTime) {
                return $endFlashSalesTime;
            }
        }
        return 0;
    }
}