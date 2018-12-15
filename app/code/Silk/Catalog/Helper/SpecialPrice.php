<?php

/**
 * User: Bob song <song01140228@163.com>
 * @date 18-9-17 下午6:40
 */


namespace Silk\Catalog\Helper;


class SpecialPrice extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\ScopeResolverInterface
     */
    protected $_scopeResolver;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $_dateTime;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;


    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\ScopeResolverInterface $scopeResolver,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Stdlib\DateTime $dateTime
    )
    {
        $this->_scopeResolver = $scopeResolver;
        $this->_dateTime = $dateTime;
        $this->localeDate = $localeDate;
        parent::__construct($context);
    }


    /**
     * @param $scope
     * @param null $dateFrom
     * @param null $dateTo
     * @return bool
     */
    public function isScopeDateInInterval($scope, $dateFrom = null, $dateTo = null)
    {
        $dateFrom = $this->localeDate->date(new \DateTime($dateFrom))->format('Y-m-d H:i:s');
        $dateTo = $this->localeDate->date(new \DateTime($dateTo))->format('Y-m-d H:i:s');

        if (!$scope instanceof \Magento\Framework\App\ScopeInterface) {
            $scope = $this->_scopeResolver->getScope($scope);
        }

        $scopeTimeStamp = $this->localeDate->scopeTimeStamp($scope);
        $fromTimeStamp = strtotime($dateFrom);
        $toTimeStamp = strtotime($dateTo);

        $result = !(!$this->_dateTime->isEmptyDate($dateFrom) && $scopeTimeStamp < $fromTimeStamp)
            && !(!$this->_dateTime->isEmptyDate($dateTo) && $scopeTimeStamp > $toTimeStamp);
        return $result;
    }
}