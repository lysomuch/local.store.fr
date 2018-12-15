<?php
/**
 * Created by PhpStorm.
 * User: cristen
 * Date: 2018/5/17
 * Time: 10:24
 */

namespace Silk\Cms\Block;

use Magento\Framework\Exception\NoSuchEntityException;

class Product extends \Magento\Framework\View\Element\Template
{
    protected $_productFactory;

    /**
     * @var \Magento\Review\Model\ReviewFactory
     */
    protected $_reviewFactory;


    /** @var \Magento\Framework\App\Cache */
    protected $cache;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $_priceHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Framework\App\Cache $cache,
        array $data = []
    )
    {
        $this->_productFactory = $productFactory;
        $this->cache = $cache;
        $this->_reviewFactory = $reviewFactory;
        $this->_priceHelper = $priceHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get Product By Sku
     * @return \Magento\Catalog\Api\Data\ProductInterface|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductDataBySku()
    {
        $sku = trim($this->getSku());
        if ($sku) {
            try {
                $storeId = $this->_storeManager->getStore()->getId();
                $cacheKey = 'homepage_cms_' . $storeId . $sku;
                $productJson = $this->cache->load($cacheKey);
                $productData = json_decode($productJson, true);
                if (!$productJson) {
                    $productModel = $this->_productFactory->create();
                    if ($storeId !== null) {
                        $productModel->setData('store_id', $storeId);
                    }
                    $product = $productModel->loadByAttribute('sku', $sku);
                    $productData = array('id'=> '', 'final_price'=> '', 'price'=> '', 'name'=> '', 'rating_summary'=> '', );
                    if ($product) {
                        $productData['id'] = $product->getId();
                        $productData['final_price'] = $product->getFinalPrice();
                        $productData['price'] = $product->getPrice();
                        $productData['name'] = $product->getName();
                        $productData['rating_summary'] = $this->getRatingSummary($product, $storeId);
                        $productJson = json_encode($productData);
                        $this->cache->save($productJson, $cacheKey, ['block_html'], 86400);
                    }
                }
            }catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                return array('id'=> '', 'final_price'=> '', 'price'=> '', 'name'=> '', 'rating_summary'=> '', );
            }
            return $productData;
        } else {
            return array('id'=> '', 'final_price'=> '', 'price'=> '', 'name'=> '', 'rating_summary'=> '', );
        }
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param $storeId
     * @return int
     */
    public function getRatingSummary($product, $storeId)
    {
        $ratingSummary = 0;
        if($product->getId()) {
            $this->_reviewFactory->create()->getEntitySummary($product, $storeId);
            $ratingSummary = $product->getRatingSummary()->getRatingSummary();
        }

        return $ratingSummary? $ratingSummary : 0;
    }

    /**
     * 格式化价格
     * @param $price
     * @return float|string
     */
    public function formatPrice($price)
    {
        return $this->_priceHelper->currency($price, true, false);
    }
}