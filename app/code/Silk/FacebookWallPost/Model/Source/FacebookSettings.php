<?php
namespace Silk\FacebookWallPost\Model\Source;

class FacebookSettings
{
    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * Get Facebook setting
     *
     * @return string
     */
    public function getFacebookSetting($path)
    {
        $scope_stores = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $value = $this->_scopeConfig->getValue($path, $scope_stores);

        if ($value == null) {
            $websites = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES;
            $value = $this->_scopeConfig->getValue($path, $websites);
        }

        if ($value == null) {
            $value = $this->_scopeConfig->getValue($path);
        }

        if ($value) {
            return $value;
        }

        return '';
    }
}
