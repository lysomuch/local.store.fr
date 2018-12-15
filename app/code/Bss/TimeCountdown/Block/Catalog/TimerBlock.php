<?php
namespace Bss\TimeCountdown\Block\Catalog;

class TimerBlock extends \Magento\Framework\View\Element\Template
{
    protected $_template = null;
    protected $_objectManager = null;
    //protected $_scopeConfig = null;
    protected $_productRepository = null;
	
    public function setTimerTemplate($type)
    {
        if ($type === 'catalog') {
           $this->setTemplate('Bss_TimeCountdown::catalog/product/list-countdown.phtml');
        } elseif ($type === 'product') {
           $this->setTemplate('Bss_TimeCountdown::catalog/product/view-countdown.phtml');
        } else {

            $this->setTemplate(null);
        }
        return $this;
    }

    private function getObjectManager() {
        if (empty($this->_objectManager)) {
            $this->_objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        }
        return $this->_objectManager;
    }

    /*private function getScopeConfig() {
        if (empty($this->_scopeConfig)) {
            $this->_scopeConfig = $this->getObjectManager()->get('\Magento\Framework\App\Config\ScopeConfigInterface'); 
        }
        return $this->_scopeConfig;
    }*/
 
    private function getProductRepository() {
        if (empty($this->_productRepository)) {
            $this->_productRepository = $this->getObjectManager()->get('\Magento\Catalog\Model\ProductRepository'); 
        }
        return $this->_productRepository;
    }

    public function getEnableCatalog() {
        return $this->_scopeConfig->getValue(
            'timeCountdown/bss_catalog/enable_catalog', 
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getTimerCatalogId() {
        return $this->_scopeConfig->getValue(
            'timeCountdown/bss_catalog/timer_catalog_id', 
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    //判断该产品是否属于指定timer类目
    public function isProductInCatalog($productId) {
        if ($productId) {
            $categoryIds = $this->getProductRepository()->getById($productId)->getCategoryIds();
            $timerCatalogId = $this->getTimerCatalogId();

            if (in_array($timerCatalogId, $categoryIds)) {
                return true;
            }
        }
        return false;
    }
}
