<?php
/**
 * Created by PhpStorm.
 * User: cristen
 * Date: 2018/5/18
 * Time: 10:40
 */
namespace Silk\ProductList\Block\Product;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Config;

class CustomList extends ListProduct
{
    protected $_catalogConfig;
    protected $productCollection;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        array $data = [],
        Config $catalogConfig,
        CollectionFactory $collectionFactory
    )
    {
        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);

        $this->_catalogConfig = $catalogConfig;
        $this->productCollection = $collectionFactory->create();
    }

    public function getLoadedProductCollection()
    {
        return $this->_productCollection;
    }

    /**
     * @param array $filter
     */
    public function setProductCollection(array $filter=[])
    {
        $this->productCollection
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect($this->_catalogConfig->getProductAttributes())
            ->addAttributeToSelect(['is_best_seller', 'is_hot_search', 'is_limit_quantity', 'custom_tag'])
            ->addUrlRewrite();

        // 确保该产品是可见的
        $this->productCollection->addAttributeToFilter('visibility', array('neq' => 1));
        // 确保该产品是启用的
        $this->productCollection->addAttributeToFilter('status', 1);

        //其它过滤条件
        foreach($filter as $field=>$value) {
            $this->productCollection->addAttributeToFilter($field, $value);
        }

        $this->_productCollection = $this->productCollection;
//        $products = $this->productCollection->load();
    }

}