<?php
namespace Silk\Webapi\Model;
use Silk\Webapi\Api\CategoriesAndProductsInterface;

class CategoriesAndProducts extends AbstractApi implements CategoriesAndProductsInterface
{
    protected $request;
    protected $productFactory;
    protected $categoryFactory;
    protected $result;
    protected $storeManager;
    protected $productHelper;
    protected $categoryHelper;
    protected $categoryRepository;
    protected $_appEmulation;
    protected $scopeConfig;
    protected $_priceHelper;

    protected $cache;

    /**
     * @var \Magento\Catalog\Helper\ImageFactory
     */
    protected $imageHelperFactory;

    public function __construct(
        Context $context,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Store\Model\App\Emulation $appEmulation,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Helper\ImageFactory $imageHelperFactory,
        \Magento\Framework\App\Cache $cache
    )
    {
        $this->request = $request;
        $this->productFactory = $productFactory;
        $this->categoryFactory = $categoryFactory;
        $this->storeManager = $storeManager;
        $this->productHelper = $productHelper;
        $this->categoryHelper = $categoryHelper;
        $this->categoryRepository = $categoryRepository;
        $this->_appEmulation = $appEmulation;
        $this->scopeConfig = $scopeConfig;
        $this->_priceHelper = $priceHelper;
        $this->imageHelperFactory = $imageHelperFactory;
        $this->cache = $cache;
        parent::__construct($context);
    }


    /**
     * 获取首页所有分类及其产品列表
     *
     * @api
     * @return \Silk\Webapi\Api\Data\ResultInterface
     */
    public function get_categories_and_products()
    {
        $cacheKey = 'homepage_megamenu';
        $result = $this->resultFactory->create();

        $listJson = $this->cache->load($cacheKey);
        $list = json_decode($listJson, true);
        if (!$listJson) {
            $list = [];
            //获取Vertical Navigation及其产品列表
            $list['nav'] = $this->_getData('vertical_navigation');

            //获取Horizontal Navigation及其产品列表
            $list['hot'] = $this->_getData('horizontal_navigation');
            $listJson = json_encode($list);
            $this->cache->save($listJson, $cacheKey, ['block_html'], 86400);
        }

        $result->setCode(200);
        $result->setMessage('success');
        $result->setResult($list);

        return $result;
    }

    /**
     * 获取导航及其产品列表
     * @param string $type
     * @return array
     */
    protected function _getData($type='')
    {
        // 读取配置的产品数量
        $showNum = $this->_getShowNumberConfig($type);

        //获取分类列表
        $categoryCollection = $this->categoryFactory->create()->getCollection();
        $categoryCollection->addFieldToSelect(['name']);

        if($type == 'horizontal_navigation') {
            //获取后台配置的热销分类id
            $bestSellerCategoryId = $this->_getCategorySettingConfig();
            $categoryCollection->addAttributeToFilter('parent_id', $bestSellerCategoryId);
        }else {
            $categoryCollection->addAttributeToFilter('level', 2);
        }

        //确保该分类是启用的
        $categoryCollection->addAttributeToFilter('is_active', 1);

        $categoryCollection->addAttributeToFilter('include_in_menu', 1);
        $categoryCollection->setOrder('position','ASC');

        $categoryList = [];
        foreach ($categoryCollection as $category){
            //获取产品列表
            $products = $this->_getProductList($category, $showNum, $type);
            $categoryList[] = $products;
        }

        return $categoryList;
    }

    /**
     * 获取分类产品列表
     * @param \Magento\Catalog\Model\Category\Interceptor $category
     * @param $showNum
     * @param $type
     * @return array
     */
    protected function _getProductList($category, $showNum, $type)
    {
        $productCollection = $category->getProductCollection();
        $productCollection->addMinimalPrice()->addUrlRewrite();
        $productCollection->addAttributeToSelect(['name', 'small_image',]);

        // 确保该产品是可见的
        $productCollection->addAttributeToFilter('visibility', array('neq' => 1));
        // 确保该产品是启用的
        $productCollection->addAttributeToFilter('status', 1);

        // 限制该 collection 的结果
        $productCollection->setPageSize($showNum);

        //排序
        $productCollection->setOrder('position','ASC');

        //组装数据
        $productsArr = [];
        $productsArr['category_id'] = $category->getData('entity_id');
        $productsArr['title'] = $category->getData('name');
        $productsArr['url'] = $this->categoryHelper->getCategoryUrl($category);
        $productsArr['products'] = [];
        foreach ($productCollection as $product){
            $data = $product->getData();

            $arr = [
                'product_id' => $data['entity_id'],
                'name' => $data['name'],
                'img' => $this->getImageUrl($product),
                'url' => $this->productHelper->getProductUrl($product)
            ];

            if($type == 'horizontal_navigation') {
                $arr['price'] = $this->_priceHelper->currency($data['price'], true, false);
                $arr['final_price'] = $this->_priceHelper->currency($data['final_price'], true, false);
            }

            $productsArr['products'][] = $arr;
        }
        return $productsArr;
    }

    /**
     * 获取产品缩略图
     * @param object $product
     * @param string $imageId 可选值为主题/etc/view.xml中image标签id属性值
     * @return string
     */
    public function getImageUrl($product, $imageId='product_small_image')
    {
        $storeId = $this->storeManager->getStore()->getId();
        $this->_appEmulation->startEnvironmentEmulation($storeId, \Magento\Framework\App\Area::AREA_FRONTEND, true);
//        $image_url = $this->productHelper->getImageUrl($product);
        $imageUrl = $this->imageHelperFactory->create()->init($product, $imageId)->getUrl();
        $this->_appEmulation->stopEnvironmentEmulation();
        return $imageUrl;
    }

    protected function _getShowNumberConfig($field) {
        return $this->scopeConfig->getValue('catalog/home_page_module_show_products_number/' . $field, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    protected function _getCategorySettingConfig() {
        return $this->scopeConfig->getValue('catalog/category_setting/best_seller_category_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}