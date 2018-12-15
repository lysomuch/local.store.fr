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
namespace Bss\TimeCountdown\Block\Widget;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Widget\Block\BlockInterface;

class TimeCountdown extends AbstractProduct implements BlockInterface {

    const DEFAULT_FROMDATE = false;

    const DEFAULT_TODATE = false;

    const DEFAULT_ENABLE_WIDGET = true;

    const DEFAULT_ENABLE_START_TIME = true;

    const DEFAULT_ENABLE_MESS_START_TIME = false;

    const DEFAULT_MESS_START_TIME = '';

    const DEFAULT_FONT_SIZE_START_TIME = 1;

    const DEFAULT_COLOR_START_TIME = 'black';

    const DEFAULT_STYLE_START_TIME = 'default';

    const DEFAULT_ENABLE_END_TIME = true;

    const DEFAULT_ENABLE_MESS_END_TIME = false;

    const DEFAULT_MESS_END_TIME = '';

    const DEFAULT_FONT_SIZE_END_TIME = 1;

    const DEFAULT_COLOR_END_TIME = 'black';

    const DEFAULT_STYLE_END_TIME = 'default';

    /**
     * @var \Bss\TimeCountdown\Helper\Data
     */
    protected $helper;
    /**
     * @var \Bss\TimeCountdown\Helper\ModuleConfig
     */
    protected $helperConfig;

    /**
     * TimeCountdown constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Bss\TimeCountdown\Helper\ModuleConfig $helperConfig
     * @param \Bss\TimeCountdown\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Bss\TimeCountdown\Helper\ModuleConfig $helperConfig,
        \Bss\TimeCountdown\Helper\Data $helper,
        array $data = []
    )
    {
        $this->helperConfig=$helperConfig;
        $this->helper=$helper;
        parent::__construct($context,$data);
    }

    /**
     * @return $this
     */
    public function setCache(){
        return $this->setData('cache_lifetime', '0');
    }
    /**
     * @return string
     */
    public function getUniqueKey()
    {
        $key = uniqid();
        return $key;
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        if(!$this->isEnableWidgetTime() || !$this->helperConfig->isEnableModuleTimeCountdown()) {
            return '';
        }
        if(!$this->getTemplate()){
            $this->setTemplate("widget/bsstimecountdown.phtml");
        }
        $this->setTemplate($this->getTemplate());
        return parent::_toHtml();
    }

    /**
     * @return array|bool
     */
    public function getTypeTimer() {
        $fromDate = $this->getFromdate();
        $toDate = $this->getTodate();
        $isEnableStart = $this->isEnableStartTime();
        $isEnableEnd =  $this->isEnableEndTime();
        return $this->helper->conditionTimeWIdget($fromDate,$toDate,$isEnableStart,$isEnableEnd);
    }

    /**
     * @return mixed
     */
    public function getFromdate() {
        if (!$this->hasData('from_date')) {
            $this->setData('from_date', self::DEFAULT_FROMDATE);
        }
        return $this->getData('from_date');
    }

    /**
     * @return mixed
     */
    public function getTodate() {
        if (!$this->hasData('to_date')) {
            $this->setData('to_date', self::DEFAULT_TODATE);
        }
        return $this->getData('to_date');
    }

    /**
     * @return mixed
     */
    public function isEnableModuleTimeCountdown () {
        return $this->helperConfig->isEnableModuleTimeCountdown();
    }

    /**
     * @return mixed
     */
    public function isEnableWidgetTime () {
        if (!$this->hasData('enable_widget_time')) {
            $this->setData('enable_widget_time', self::DEFAULT_ENABLE_WIDGET);
        }
        return $this->getData('enable_widget_time');
    }

    /**
     * @return mixed
     */
    public function isEnableStartTime () {
        if (!$this->hasData('enable_start_time')) {
            $this->setData('enable_start_time', self::DEFAULT_ENABLE_START_TIME);
        }
        return $this->getData('enable_start_time');
    }

    /**
     * @return mixed
     */
    public function enableMessStart () {
        if (!$this->hasData('enable_mess_start')) {
            $this->setData('enable_mess_start', self::DEFAULT_ENABLE_MESS_START_TIME);
        }
        return $this->getData('enable_mess_start');
    }

    /**
     * @return mixed
     */
    public function getMessStart () {
        if (!$this->hasData('mess_start')) {
            $this->setData('mess_start', self::DEFAULT_MESS_START_TIME);
        }
        return $this->getData('mess_start');
    }

    /**
     * @return mixed
     */
    public function getFontSizeStart () {
        if (!$this->hasData('font_size_start')) {
            $this->setData('font_size_start', self::DEFAULT_FONT_SIZE_START_TIME);
        }
        return $this->getData('font_size_start');
    }

    /**
     * @return mixed
     */
    public function getColorStart () {
        if (!$this->hasData('font_color_start')) {
            $this->setData('font_color_start', self::DEFAULT_COLOR_START_TIME);
        }
        return $this->getData('font_color_start');
    }

    /**
     * @return mixed
     */
    public function getStyleStart () {
        if (!$this->hasData('style_start_stime')) {
            $this->setData('style_start_stime', self::DEFAULT_STYLE_START_TIME);
        }
        return $this->getData('style_start_stime');
    }

    /**
     * @return mixed
     */
    public function isEnableEndTime () {
        if (!$this->hasData('enable_end_time')) {
            $this->setData('enable_end_time', self::DEFAULT_ENABLE_END_TIME);
        }
        return $this->getData('enable_end_time');
    }

    /**
     * @return mixed
     */
    public function enableMessEnd () {
        if (!$this->hasData('enable_mess_end')) {
            $this->setData('enable_mess_end', self::DEFAULT_ENABLE_MESS_END_TIME);
        }
        return $this->getData('enable_mess_end');
    }

    /**
     * @return mixed
     */
    public function getMessEnd () {
        if (!$this->hasData('mess_end')) {
            $this->setData('mess_end', self::DEFAULT_MESS_END_TIME);
        }
        return $this->getData('mess_end');
    }

    /**
     * @return mixed
     */
    public function getFontSizeEnd () {
        if (!$this->hasData('font_size_end')) {
            $this->setData('font_size_end', self::DEFAULT_FONT_SIZE_END_TIME);
        }
        return $this->getData('font_size_end');
    }

    /**
     * @return mixed
     */
    public function getColorEnd () {
        if (!$this->hasData('font_color_end')) {
            $this->setData('font_color_end', self::DEFAULT_COLOR_END_TIME);
        }
        return $this->getData('font_color_end');
    }

    /**
     * @return mixed
     */
    public function getStyleEnd () {
        if (!$this->hasData('style_end_stime')) {
            $this->setData('style_end_stime', self::DEFAULT_STYLE_END_TIME);
        }
        return $this->getData('style_end_stime');
    }
}
