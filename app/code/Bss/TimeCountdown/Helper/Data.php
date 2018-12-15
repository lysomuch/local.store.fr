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

use Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {
    /**
     * @var \Magento\CatalogRule\Model\Rule
     */
    protected $rule;
    /**
     * @var \Bss\TimeCountdown\Model\ResourceModel\ResourceRule
     */
    protected $resourceRule;
    /**
     * @var ModuleConfig
     */
    protected $helperConfig;
    /** 
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface 
     */
    protected $date;

    /**
     * Data constructor.
     * @param Context $context
     * @param \Magento\CatalogRule\Model\Rule $rule
     * @param \Bss\TimeCountdown\Model\ResourceModel\ResourceRule $resourceRule
     * @param ModuleConfig $helperConfig
     */
    public function __construct(
        Context $context,
        \Magento\CatalogRule\Model\Rule $rule,
        \Bss\TimeCountdown\Model\ResourceModel\ResourceRule $resourceRule,
        \Bss\TimeCountdown\Helper\ModuleConfig $helperConfig,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
    )
    {
        $this->rule = $rule;
        $this->resourceRule = $resourceRule;
        $this->helperConfig = $helperConfig;
        $this->date =  $date;
        parent::__construct($context);
    }

    /**
     * @param $fromDate
     * @param $toDate
     * @param $price
     * @return bool
     */
    private function inLimitTime ($fromDate, $toDate, $price) {
        $dateTimeZone = $this->helperConfig->getDateTimeZone();
        $timeZone = strtotime($dateTimeZone);
        $fromTime = strtotime($fromDate);
        $toTime = strtotime($toDate);
        if($fromTime == null && $toTime == null && $price > 0) {
            return true;
        } else if($fromTime == null && $toTime != null && $price > 0) {
            if($toTime > $timeZone) {
                return true;
            } else {
                return false;
            }
        } else if($toTime == null && $fromTime != null && $price > 0) {
            if($fromTime < $timeZone) {
                return true;
            } else {
                return false;
            }
        } else {
            if($fromTime < $timeZone && $toTime > $timeZone) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * @param $fromDate
     * @return bool
     */
    private function isStartTime($fromDate) {
        $dateTimeZone = $this->helperConfig->getDateTimeZone();
        $timeZone = strtotime($dateTimeZone);
        $time = strtotime($fromDate);
        if(($time - $timeZone) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $product
     * @return array|null
     * @throws \Zend_Db_Statement_Exception
     */
    public function getPriceAndDate ($product) {
        $fromDateSpecialPrice = $this->date->date(new \DateTime($product->getSpecialFromDate()))->format('Y-m-d H:i:s');
        $toDateSpecialPrice = $this->date->date(new \DateTime($product->getSpecialToDate()))->format('Y-m-d H:i:s');
        //$toTimeSpecialPrice = strtotime($toDateSpecialPrice);//strtotime($toDateSpecialPrice) + 3600 * 24;
        //$toDateSpecialPrice = date('Y-m-d H:i:s',$toTimeSpecialPrice);

        $SpecialPrice = $product->getSpecialPrice();
        $getPrice = $product->getPrice();
        $rulePrice = $this->rule->calcProductPriceRule($product,$getPrice);

        $resourceRule = $this->resourceRule->getFromdateAndTodateCatalogRule($product->getId());
        $fromDateRule = '';
        $toDateRule = '';
        if(isset($resourceRule['0'])) {
            $fromDateRule = array_key_exists('from_date',$resourceRule['0']) ? $resourceRule['0']['from_date'] : '';
            $toDateRule = array_key_exists('to_date',$resourceRule['0']) ? $resourceRule['0']['to_date'] : '';
        }

        if($toDateRule) {
            $toTimeRule = strtotime($toDateRule) + 3600 * 24;
            $toDateRule = date('Y-m-d H:i:s',$toTimeRule);
        }

        $inLimitTimeRule = $this->inLimitTime($fromDateRule, $toDateRule, $rulePrice);
        $inLimitTimeSpecial = $this->inLimitTime($fromDateSpecialPrice, $toDateSpecialPrice, $SpecialPrice);
        $isStartTimeRule = $this->isStartTime($fromDateRule);
        $isStartTimeSpecial = $this->isStartTime($fromDateSpecialPrice);

        $toDateSpecialPrice = ($toDateSpecialPrice == NULL) ? '0' : $toDateSpecialPrice;
        $toDateRule = ($toDateRule == NULL) ? '0' : $toDateRule;

        if(!$inLimitTimeRule && !$inLimitTimeSpecial && $isStartTimeRule && $isStartTimeSpecial) {
            $fromTimeRule = strtotime($fromDateRule);
            $fromTimeSpecial = strtotime($fromDateSpecialPrice);
            if($fromTimeRule < $fromTimeSpecial) {
                return ['price' => $rulePrice, 'fromDate' => $fromDateRule, 'toDate' => $toDateRule];
            } else {
                return ['price' => $SpecialPrice, 'fromDate' => $fromDateSpecialPrice, 'toDate' => $toDateSpecialPrice];
            }
        }else {
            if ($rulePrice < $SpecialPrice && $rulePrice < $getPrice) {
                if($inLimitTimeRule || $isStartTimeRule && !$inLimitTimeSpecial) {
                    return ['price' => $rulePrice, 'fromDate' => $fromDateRule, 'toDate' => $toDateRule];
                } else if($SpecialPrice < $getPrice && ($inLimitTimeSpecial || $isStartTimeSpecial && !$inLimitTimeRule)) {
                    return ['price' => $SpecialPrice, 'fromDate' => $fromDateSpecialPrice, 'toDate' => $toDateSpecialPrice];
                } else {
                    return null;
                }
            } else if($SpecialPrice < $getPrice && ($inLimitTimeSpecial || $isStartTimeSpecial && !$inLimitTimeRule)) {
                 return ['price' => $SpecialPrice, 'fromDate' => $fromDateSpecialPrice, 'toDate' => $toDateSpecialPrice];
            } else if( $rulePrice < $getPrice && ($inLimitTimeRule || $isStartTimeRule && !$inLimitTimeSpecial)) {
                return ['price' => $rulePrice, 'fromDate' => $fromDateRule, 'toDate' => $toDateRule];
            } else {
                return null;
            }
        }
    }

    /**
     * @param $fromDate
     * @param $toDate
     * @param $isEnableStart
     * @param $isEnableEnd
     * @return array|bool
     */
    public function conditionTimeWIdget ($fromDate, $toDate, $isEnableStart, $isEnableEnd) {

        $fromTimeRest = strtotime($fromDate);
        $toTimeRest = strtotime($toDate);

        if($isEnableStart) {
            return ['type' => 'start_time', 'time_rest' => $fromTimeRest];
        }

        if($isEnableEnd) {
            return ['type' => 'end_time', 'time_rest' => $toTimeRest];
        }
        return false;
    }
}
