<?php

/**
 * User: Bob song <song01140228@163.com>
 * @date 18-5-23 下午2:13
 */


namespace Silk\FlashSales\Block;

use Magento\Framework\Exception\NoSuchEntityException;

class CountDown extends \Magento\Framework\View\Element\Template
{
    protected $_productFactory;

    /** @var \Magento\Framework\App\Cache */
    protected $cache;

    protected $_productData = [];

    /** @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface */
    protected $date;

    /**
     * CountDown constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\App\Cache $cache
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\App\Cache $cache,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        array $data =[]
    )
    {
        $this->_productFactory = $productFactory;
        $this->cache = $cache;
        $this->date =  $date;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    public function getProductData()
    {
        if (!$this->_productData) {
            $sku = trim($this->getSku());
            if ($this->getSku()) {
                try {
                    $storeId = $this->_storeManager->getStore()->getId();
                    $cacheKey = 'homepage_flashsales_' . $storeId . $sku;
                    $productJson = $this->cache->load($cacheKey);
                    $productData = json_decode($productJson, true);
                    if (!$productJson) {
                        $productModel = $this->_productFactory->create();
                        if ($storeId !== null) {
                            $productModel->setData('store_id', $storeId);
                        }
                        $product = $productModel->loadByAttribute('sku', $sku);
                        $productData = array('id' => '', 'is_flash_sales' => '', 'special_to_date' => '','special_from_date' => '');
                        if ($product) {
                            $productData['id'] = $product->getId();
                            $productData['is_flash_sales'] = $product->getIsFlashSales();
                            $productData['special_to_date'] = $this->date->date(new \DateTime($product->getSpecialToDate()))->format('Y-m-d H:i:s');
                            $productData['special_from_date'] = $this->date->date(new \DateTime($product->getSpecialFromDate()))->format('Y-m-d H:i:s');
                            $productJson = json_encode($productData);
                            $this->cache->save($productJson, $cacheKey, ['block_html'], 86400);
                        }
                    }
                    $this->_productData = $productData;
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $this->_productData = array('id' => '', 'is_flash_sales' => '', 'special_to_date' => '','special_from_date' => '');
                }
            } else {
                $this->_productData = array('id' => '', 'is_flash_sales' => '', 'special_to_date' => '','special_from_date' => '');
            }
        }

        return $this->_productData;
    }


    /**
     * @return int|mixed
     */
    public function getEndTime()
    {
        $productData = $this->getProductData();
        if ($productData['is_flash_sales']) {
            $endFlashSalesTime = $productData['special_to_date'];
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
        $productData = $this->getProductData();
        if ($productData['is_flash_sales']) {
            $endFlashSalesTime = $productData['special_from_date'];
            if ($endFlashSalesTime) {
                return $endFlashSalesTime;
            }
        }
        return 0;
    }

    /**
     * @return string
     */
    public function getFlashSalesUrl()
    {
        $productData = $this->getProductData();
        if ($productData['id']) {
            return $this->getUrl('flashsales/view/', array('id' => $productData['id']));
        }
        return '';
    }

    /**
     * @return bool
     */
    public function isShowFlashSales()
    {
        $productData = $this->getProductData();
        if ($productData['id']) {
            return true;
        }
        return false;
    }
}