<?php
/**
 * Created by PhpStorm.
 * User: cristen
 * Date: 2018/9/13
 * Time: 9:50
 */

namespace Silk\Catalog\Controller\Nav;
use Magento\Framework\App\Action;
class Ajax extends \Magento\Framework\App\Action\Action
{
    protected $categoryFactory;
    protected $productHelper;
    protected $categoryHelper;
    protected $scopeConfig;
    protected $_priceHelper;
    protected $cache;
    /** @var \Magento\Catalog\Helper\ImageFactory */
    protected $imageHelperFactory;
    /** @var \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory */
    protected $jsonEncoder;
    protected $_productloader;

    public function __construct(
        Action\Context $context,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Helper\ImageFactory $imageHelperFactory,
        \Magento\Framework\App\Cache $cache,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    )
    {
        $this->categoryFactory = $categoryFactory;
        $this->productHelper = $productHelper;
        $this->categoryHelper = $categoryHelper;
        $this->scopeConfig = $scopeConfig;
        $this->_priceHelper = $priceHelper;
        $this->imageHelperFactory = $imageHelperFactory;
        $this->cache = $cache;
        $this->jsonEncoder = $resultJsonFactory;
        $this->_productloader = $_productloader;
        parent::__construct($context);
    }

    public function execute()
    {
        // 获取站点根类目和bestSeller类目（备注：因为这里获取不到当前当铺ID）
        $rootUrl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        if (strpos($rootUrl, "olightstore.ca") !== false) {
            $website = 'ca';
            $rootCatId = 2;
            $bestSellerCategoryId = 3;
        } elseif (strpos($rootUrl, "olighthk.com") !== false) {
            $website = 'uk';
            $rootCatId = 41;
            $bestSellerCategoryId = 42;
        } else {
            $website = 'ca';
            $rootCatId = 2;
            $bestSellerCategoryId = 3;
        }

        $resultJson = $this->jsonEncoder->create();
        $cacheKey = 'homepage_megamenu_'.$website;

        $listJson = $this->cache->load($cacheKey);
        $list = json_decode($listJson, true);
        if ( ! $listJson) {
            $list = [];
            //获取Vertical Navigation及其产品列表
            $list['nav'] = $this->_getData('vertical_navigation',$rootCatId,$bestSellerCategoryId);

            //获取Horizontal Navigation及其产品列表
            $list['hot'] = $this->_getData('horizontal_navigation',$rootCatId,$bestSellerCategoryId);
            $listJson = json_encode($list);
            $this->cache->save($listJson, $cacheKey, ['block_html'], 86400);
        }

        $data = [
            'code' => 200,
            'message' => 'success',
            'result' => $list
        ];

        return $resultJson->setJsonData(json_encode($data));
    }

    /**
     * 获取导航及其产品列表
     * @param string $type
     * @return array
     */
    protected function _getData($type='',$rootCatId=2,$bestSellerCategoryId=3)
    {

        // 读取配置的产品数量
        $showNum = $this->_getShowNumberConfig($type);

        //获取分类列表
        $categoryCollection = $this->categoryFactory->create()->getCollection();
        $categoryCollection->addFieldToSelect(['name']);

        if($type == 'horizontal_navigation') {
            //获取后台配置的热销分类id
            //$bestSellerCategoryId = $this->_getCategorySettingConfig();
            $categoryCollection->addAttributeToFilter('parent_id', $bestSellerCategoryId);
        }else {
            $categoryCollection->addAttributeToFilter('level', 2);
            $categoryCollection->addAttributeToFilter('parent_id', $rootCatId);
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
                $final_price = (float)$data['price'] == (float)$data['final_price'] ? $data['final_price'] : $this->_productloader->create()->load($data['entity_id'])->getFinalPrice();
                $arr['final_price'] = $this->_priceHelper->currency($final_price, true, false);
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
        $imageUrl = $this->imageHelperFactory->create()->init($product, $imageId)->getUrl();
        return $imageUrl;
    }

    protected function _getShowNumberConfig($field) {
        return $this->scopeConfig->getValue('catalog/home_page_module_show_products_number/' . $field, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    protected function _getCategorySettingConfig() {
        return $this->scopeConfig->getValue('catalog/category_setting/best_seller_category_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}