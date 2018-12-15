<?php
namespace Silk\Cms\Block;

use Magento\Framework\Exception\NoSuchEntityException;

class Countdown extends \Magento\Framework\View\Element\Template
{
    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    protected $_scope;

    /** @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface */
    protected $_date;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        array $data = []
    )
    {
        $this->_scope = $scopeConfig;
        $this->_date = $date;
        parent::__construct($context, $data);
    }

    /**
     * 首页倒计时开启开关
     * @author gfh
     * @return string
     */
    public function getTimerEnable() {
        try {
            $timerEnable = $this->_scope->getValue('cms/header_timer/timer_enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $timerEnable = empty($timerEnable) ? '' : trim($timerEnable);
            return $timerEnable;
        } catch (Exception $e) {
            return ;
        }
    }
	
    /**
     * 首页倒计时的开始时间
     * @author gfh
     * @return string
     */
    public function getTimerBeginDate() {
        try {
            $beginDate = $this->_scope->getValue('cms/header_timer/timer_begin_date', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $beginDate = empty($beginDate) ? '' : trim($beginDate);
            return $beginDate;
        } catch (Exception $e) {
            return ;
        }
    }

    /**
     * 首页倒计时的结束时间
     * @author gfh
     * @return string
     */
    public function getTimerEndDate() {
        try {
            $endDate = $this->_scope->getValue('cms/header_timer/timer_end_date', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $endDate = empty($endDate) ? '' : trim($endDate);
            return $endDate;
        } catch (Exception $e) {
            return ;
        }
    }

    /**
     * 获取系统当前时区
     * @author gfh
     * @return string
     */
    public function getLocaleTimezone() {
        try {
            $timezone = $this->_scope->getValue('general/locale/timezone', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            return $timezone;
        } catch (Exception $e) {
            return ;
        }
    }

    /**
     * 获取倒计时提示的信息
     * @author gfh
     * @return string
     */
    public function getTimerMessage() {
        try {
            $timerMsg = $this->_scope->getValue('cms/header_timer/timer_msg', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $timerMsg = empty($timerMsg) ? '' : trim($timerMsg);
            return $timerMsg;
        } catch (Exception $e) {
            return ;
        }
    }
}