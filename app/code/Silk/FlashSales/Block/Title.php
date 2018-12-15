<?php
/**
 * Created by PhpStorm.
 * User: cristen
 * Date: 2018/9/17
 * Time: 17:32
 */

namespace Silk\FlashSales\Block;

class Title extends \Magento\Theme\Block\Html\Title
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $productHelper;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $_coreRegistry,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Helper\Product $productHelper,
        array $data = []
    ) {
        $this->_coreRegistry = $_coreRegistry;
        $this->productRepository = $productRepository;
        $this->productHelper = $productHelper;
        parent::__construct($context, $data);
    }

    public function getProduct()
    {
        $productId = $this->getRequest()->getParam('id');

        if (!$this->_coreRegistry->registry('product') && $productId) {
            $product = $this->productRepository->getById($productId);
            $this->_coreRegistry->register('product', $product);
        }
        return $this->_coreRegistry->registry('product');
    }

    public function getProductUrl()
    {
        $product = $this->getProduct();
        return $this->productHelper->getProductUrl($product);
    }
}