<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_TimeCountdown
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\TimeCountdown\Helper;

class ProductData extends \Magento\Framework\Url\Helper\Data
{
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    private $productInfo;
    /**
     * @var \Magento\CatalogInventory\Model\StockRegistry
     */
    private $stockRegistry;
    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    private $configurableData;
    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $localeCurrency;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var Data
     */
    protected $helper;
    /**
     * @var ModuleConfig
     */
    protected $helperConfig;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $datetime;

    /**
     * ProductData constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Model\ProductRepository $productInfo
     * @param \Magento\CatalogInventory\Model\StockRegistry $stockRegistry
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableData
     * @param ModuleConfig $helperConfig
     * @param Data $helper
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\ProductRepository $productInfo,
        \Magento\CatalogInventory\Model\StockRegistry $stockRegistry,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableData,
        \Bss\TimeCountdown\Helper\ModuleConfig $helperConfig,
        \Bss\TimeCountdown\Helper\Data $helper,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
    ) {
        $this->productInfo = $productInfo;
        $this->stockRegistry = $stockRegistry;
        $this->configurableData = $configurableData;
        $this->helperConfig = $helperConfig;
        $this->helper=$helper;
        $this->localeCurrency = $localeCurrency;
        $this->storeManager = $storeManager;
        $this->datetime = $date;
        parent::__construct($context);
    }

    /**
     * @return string
     */
    public function getCurrentCurrencySymbol()
    {
      $code = $this->storeManager->getStore()->getCurrentCurrencyCode();
      return $this->localeCurrency->getCurrency($code)->getSymbol();
    }

    /**
     * @param $productEntityId
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Db_Statement_Exception
     */
    public function getAllData($productEntityId)
    {
        $result = [];
        $parentProduct = $this->configurableData->getChildrenIds($productEntityId);
        $result['entity'] = $productEntityId;
        
        foreach ($parentProduct[0] as $simpleProduct) {
            $childProduct = [];
            $childProduct['entity'] = $simpleProduct;
            $child = $this->productInfo->getById($childProduct['entity']);
            $childStock = $this->stockRegistry->getStockItem($childProduct['entity']);
            $childProduct['stock_number'] = $childStock->getQty();
            $childProduct['sku'] = $child->getSku();
            $childProduct['timeCountdown'] = $this->getInfoDisplayProductPage($child);
            
            $result['child'][$simpleProduct] = $childProduct;
        }
        return $result;
    }

    /**
     * @param $product
     * @return array|bool
     * @throws \Zend_Db_Statement_Exception
     */
    public function getInfoDisplayProductPage($product) {
        $dateTimeZone = $this->datetime->date()->format('Y-m-d H:i:s');
        $timeZone = strtotime($dateTimeZone);

        $configModule = $this->helperConfig->getAllConfig();

        $selected_start = $this->helperConfig->getSelectedTimerStart($product);
        $selected_end = $this->helperConfig->getSelectedTimerEnd($product);

        $enableModule = $configModule['is_enable_module'];
        $enableStartTime = $configModule['is_enable_start'];
        $enableEndTime = $configModule['is_enable_end'];

        $isDisplayProductStart = $configModule['is_product_start'];
        $isDisplayProductEnd = $configModule['is_product_end'];

        $price = $product->getPrice();
        $infoPriceAndDate = $this->helper->getPriceAndDate($product);
        $finalPrice = $infoPriceAndDate['price'];
        $fromDate = $infoPriceAndDate['fromDate'];
        $toDate = $infoPriceAndDate['toDate'];

        $numDayStart = $configModule['num_day_start'];
        $numDayEnd = $configModule['num_day_end'];

        $timeStartRest = strtotime($fromDate) - $timeZone;
        $timeEndRest = ($toDate == '0') ? 0 : strtotime($toDate) - $timeZone;

        $symbolCurrency = $this->getCurrentCurrencySymbol();
        $currentCurrencyRate = round($this->storeManager->getStore()->getCurrentCurrencyRate(), 2);

        if($enableModule) {
            $saleValue = ($price - $finalPrice) * $currentCurrencyRate;
            $price = ($price > 0) ? ($price * $currentCurrencyRate) : 1;
            $percentDiscount = round($saleValue/$price, 4) * 100;
            if($enableStartTime && ($timeStartRest > 0) && ($timeStartRest/86400 < $numDayStart) && ($selected_start !== '0') && $isDisplayProductStart) {
                $indexTime = $this->indexTimeCountDown($timeStartRest);
                $data =  [
                    'type' => 'start',
                    'product_id' => $product->getId(),
                    'time_rest' => $timeStartRest,

                    'message' => '',
                    'color' => '',
                    'font_size' => '',
                    'style' => $configModule['styles_product_start'],

                    'sale_value' => '',
                    'percent_discount' => '',
                    'index_time' => $indexTime,

                    'messSaleValue' => '',
                    'corlorMessSaleValue' => '',
                    'fontSizeMessSaleValue' => '',

                    'messSalePercent' => '',
                    'corlorMessSalePercent' => '',
                    'fontSizeMessSalePercent' => '',
                ];

                if($configModule['is_mess_product_start']) {
                    $data['message'] = $configModule['mess_product_start'];
                    $data['color'] = 'color: '.$configModule['color_mess_product_start'];
                    $data['font_size'] = 'font-size: '.$configModule['font_size_mess_product_start']. 'px';
                }

                if($configModule['enable_mess_sale_value']) {
                    $data['sale_value'] = $symbolCurrency . $saleValue;
                    $data['messSaleValue'] = $configModule['mess_sale_value'];
                    $data['corlorMessSaleValue'] = 'color: '.$configModule['color_mess_sale'];
                    $data['fontSizeMessSaleValue'] = 'font-size: '.$configModule['font_size_mess_sale']. 'px';
                }

                if($configModule['enable_mess_sale_percent']) {
                    $data['percent_discount'] = $percentDiscount .'%';
                    $data['messSalePercent'] = $configModule['mess_percent'];
                    $data['corlorMessSalePercent'] = 'color: '.$configModule['color_mess_percent'];
                    $data['fontSizeMessSalePercent'] = 'font-size: '.$configModule['font_size_mess_percent']. 'px';
                }
                return $data;
            } else {
                $timeStart = false;
            }

            if($enableEndTime && ($timeEndRest >= 0) && ($timeEndRest/86400 < $numDayEnd) && ($selected_end !== '0') && !$timeStart && $isDisplayProductEnd) {
                $indexTime = $this->indexTimeCountDown($timeEndRest);
                $data =  [
                    'type' => 'end',
                    'product_id' => $product->getId(),
                    'time_rest' => $timeEndRest,

                    'message' => '',
                    'color' => '',
                    'font_size' => '',
                    'style' => $configModule['styles_product_end'],

                    'sale_value' => '',
                    'percent_discount' => '',
                    'index_time' => $indexTime,

                    'messSaleValue' => '',
                    'corlorMessSaleValue' => '',
                    'fontSizeMessSaleValue' => '',

                    'messSalePercent' => '',
                    'corlorMessSalePercent' => '',
                    'fontSizeMessSalePercent' => '',
                ];

                if($configModule['is_mess_product_end']) {
                    $data['message'] = $configModule['mess_product_end'];
                    $data['color'] = 'color: '.$configModule['color_mess_product_end'];
                    $data['font_size'] = 'font-size: '.$configModule['font_size_mess_product_end']. 'px';
                }

                if($configModule['enable_mess_sale_value']) {
                    $data['sale_value'] = $symbolCurrency . $saleValue;
                    $data['messSaleValue'] = $configModule['mess_sale_value'];
                    $data['corlorMessSaleValue'] = 'color: '.$configModule['color_mess_sale'];
                    $data['fontSizeMessSaleValue'] = 'font-size: '.$configModule['font_size_mess_sale']. 'px';
                }

                if($configModule['enable_mess_sale_percent']) {
                    $data['percent_discount'] = $percentDiscount .'%';
                    $data['messSalePercent'] = $configModule['mess_percent'];
                    $data['corlorMessSalePercent'] = 'color: '.$configModule['color_mess_percent'];
                    $data['fontSizeMessSalePercent'] = 'font-size: '.$configModule['font_size_mess_percent']. 'px';
                }
                return $data;
            } else {
                return false;
            }

        } else {
            return false;
        }
    }

    /**
     * @param $product
     * @return array|bool
     * @throws \Zend_Db_Statement_Exception
     */
    public function getInfoDisplayCatalogPage($product) {
        $configModule = $this->helperConfig->getAllConfig();
        $enableModule = $configModule['is_enable_module'];
        $enableStartTime = $configModule['is_enable_start'];
        $enableEndTime = $configModule['is_enable_end'];
        $isDisplayCatalogStart = $configModule['is_catalog_start'];
        $isDisplayCatalogEnd = $configModule['is_catalog_end'];
        $numDayStart = $configModule['num_day_start'];
        $numDayEnd = $configModule['num_day_end'];
        if($enableModule) {
            $selected_start = $this->helperConfig->getSelectedTimerStart($product);
            $selected_end = $this->helperConfig->getSelectedTimerEnd($product);

            $price = $product->getPrice();
            $infoPriceAndDate = $this->helper->getPriceAndDate($product);
            $finalPrice = $infoPriceAndDate['price'];
            $fromDate = $infoPriceAndDate['fromDate'];
            $toDate = $infoPriceAndDate['toDate'];

            $timeZone = strtotime($this->datetime->date()->format('Y-m-d H:i:s'));
            $timeStartRest = strtotime($fromDate) - $timeZone;
            $timeEndRest = ($toDate == '0') ? 0 : strtotime($toDate) - $timeZone;

            $symbolCurrency = $this->getCurrentCurrencySymbol();
            $currentCurrencyRate = round($this->storeManager->getStore()->getCurrentCurrencyRate(), 2);
            
            $saleValue = ($price - $finalPrice) * $currentCurrencyRate;
            $price = ($price > 0) ? ($price * $currentCurrencyRate) : 1;
            $percentDiscount = round($saleValue/$price, 4) * 100;
            if($enableStartTime && ($timeStartRest > 0) && ($timeStartRest/86400 < $numDayStart) && ($selected_start !== '0') && $isDisplayCatalogStart) {
                $indexTime = $this->indexTimeCountDown($timeStartRest);
                $data = [
                    'type' => 'start',
                    'product_id' => $product->getId(),
                    'time_rest' => $timeStartRest,

                    'message' => '',
                    'color' => '',
                    'font_size' => '',
                    'style' => $configModule['style_catalog_start'],

                    'sale_value' => '',
                    'percent_discount' => '',
                    'index_time' => $indexTime,

                    'messSaleValue' => '',
                    'corlorMessSaleValue' => '',
                    'fontSizeMessSaleValue' => '',

                    'messSalePercent' => '',
                    'corlorMessSalePercent' => '',
                    'fontSizeMessSalePercent' => '',
                ];
                if($configModule['is_mess_catalog_start']) {
                    $data['message'] = $configModule['mess_catalog_start'];
                    $data['color'] = 'color: '.$configModule['color_mess_catalog_start'];
                    $data['font_size'] = 'font-size: '.$configModule['font_size_mess_catalog_start']. 'px';
                }

                if($configModule['enable_mess_sale_value']) {
                    $data['sale_value'] = $symbolCurrency . $saleValue;
                    $data['messSaleValue'] = $configModule['mess_sale_value'];
                    $data['corlorMessSaleValue'] = 'color: '.$configModule['color_mess_sale'];
                    $data['fontSizeMessSaleValue'] = 'font-size: '.$configModule['font_size_mess_sale']. 'px';
                }

                if($configModule['enable_mess_sale_percent']) {
                    $data['percent_discount'] = $percentDiscount .'%';
                    $data['messSalePercent'] = $configModule['mess_percent'];
                    $data['corlorMessSalePercent'] = 'color: '.$configModule['color_mess_percent'];
                    $data['fontSizeMessSalePercent'] = 'font-size: '.$configModule['font_size_mess_percent']. 'px';
                }
                return $data;
            } else {
                $timeStart = false;
            }

            if($enableEndTime && ($timeEndRest >= 0) && ($timeEndRest/86400 < $numDayEnd) && ($selected_end !== '0') && !$timeStart && $isDisplayCatalogEnd) {
                $indexTime = $this->indexTimeCountDown($timeEndRest);
                $data = [
                    'type' => 'end',
                    'product_id' => $product->getId(),
                    'time_rest' => $timeEndRest,

                    'message' => '',
                    'color' => '',
                    'font_size' => '',
                    'style' => $configModule['style_catalog_end'],

                    'sale_value' => '',
                    'percent_discount' => '',
                    'index_time' => $indexTime,

                    'messSaleValue' => '',
                    'corlorMessSaleValue' => '',
                    'fontSizeMessSaleValue' => '',

                    'messSalePercent' => '',
                    'corlorMessSalePercent' => '',
                    'fontSizeMessSalePercent' => '',
                ];

                if($configModule['is_mess_catalog_end']) {
                    $data['message'] = $configModule['mess_catalog_end'];
                    $data['color'] = 'color: '.$configModule['color_mess_catalog_end'];
                    $data['font_size'] = 'font-size: '.$configModule['font_size_mess_catalog_end']. 'px';
                }

                if($configModule['enable_mess_sale_value']) {
                    $data['sale_value'] = $symbolCurrency . $saleValue;
                    $data['messSaleValue'] = $configModule['mess_sale_value'];
                    $data['corlorMessSaleValue'] = 'color: '.$configModule['color_mess_sale'];
                    $data['fontSizeMessSaleValue'] = 'font-size: '.$configModule['font_size_mess_sale']. 'px';
                }

                if($configModule['enable_mess_sale_percent']) {
                    $data['percent_discount'] = $percentDiscount .'%';
                    $data['messSalePercent'] = $configModule['mess_percent'];
                    $data['corlorMessSalePercent'] = 'color: '.$configModule['color_mess_percent'];
                    $data['fontSizeMessSalePercent'] = 'font-size: '.$configModule['font_size_mess_percent']. 'px';
                }
                return $data;
            } else {
                return false;
            }

        } else {
            return false;
        }
    }
    /*public function getInfoDisplayCatalogPage2($product) {
        $configModule = $this->helperConfig->getAllConfig();
        $enableModule = $configModule['is_enable_module'];//倒计时是否开启
        $isDisplayCatalogEnd = $configModule['is_catalog_end'];//是否在类目页面显示倒计时
        if($enableModule && $isDisplayCatalogEnd) {
            //当前系统时间戳
            $timeZone = strtotime($this->datetime->date()->format('Y-m-d H:i:s'));

            //产品促销开始时间和结束时间
            $infoPriceAndDate = $this->helper->getPriceAndDate($product);
            $fromDate = $infoPriceAndDate['fromDate'];
            $toDate = $infoPriceAndDate['toDate'];

            //还要多久开始
            $timeStartRest = strtotime($fromDate) - $timeZone;
            //还要多久结束
            $timeEndRest = ($toDate == '0') ? 0 : strtotime($toDate) - $timeZone;

            $timeStart = true;//默认还没有开始
            if($timeStartRest > 0) {
                $data = ['time_rest' => $timeStartRest];
                return $data;
            } else {
                $timeStart = false;//已开始或已结束
            }

            if($timeEndRest >= 0 && !$timeStart) {
                //已开始未结束
                $data = ['time_rest' => $timeEndRest];
                return $data;
            }
        }
        return false;
    }*/

    /**
     * @param $time
     * @return array
     */
    public function indexTimeCountDown ($time) {
        if($time > 0) {
            $day = floor($time/86400);
            $secondRest = $time%86400;
            $hour = floor($secondRest/3600);
            $secondRest = $secondRest%3600;
            $minute = floor($secondRest/60);
            $secondRest = $secondRest%60;
            $second = $secondRest;
            return [
                'day' => $day,
                'hour' => $hour,
                'minute' => $minute,
                'second' => $second
            ];
        } else {
            return [];
        }
    }
}
