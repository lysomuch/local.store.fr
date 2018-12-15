<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_TimeCountdown
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\TimeCountdown\Block\Widget;
class ProductListOnSale extends \Magento\CatalogWidget\Block\Product\ProductsList
{

    /**
     * @var \Bss\TimeCountdown\Helper\ModuleConfig
     */
    protected $helperProduct;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\CatalogRule\Model\RuleFactory
     */
    protected $catalogRule;
    /**
     * @var \Bss\TimeCountdown\Model\ResourceModel\ResourceProduct
     */
    protected $resourceProduct;

    const DEFAULT_TITLE_WIDGET = '';

    const DEFAULT_ENABLE_WIDGET_LIST = true;

    const DEFAULT_SHOW_SLIDE = false;

    const DEFAULT_AUTO_SLIDE = false;

    const DEFAULT_TIME_AUTO_SLIDE = 0;

    const DEFAULT_PRODUCT_PER_SLIDE = 5;

    /**
     * ProductListOnSale constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder
     * @param \Magento\CatalogWidget\Model\Rule $rule
     * @param \Magento\Widget\Helper\Conditions $conditionsHelper
     * @param \Bss\TimeCountdown\Helper\ModuleConfig $helperProduct
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\CatalogRule\Model\RuleFactory $catalogRule
     * @param \Bss\TimeCountdown\Model\ResourceModel\ResourceProduct $resourceProduct
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder,
        \Magento\CatalogWidget\Model\Rule $rule,
        \Magento\Widget\Helper\Conditions $conditionsHelper,
        \Bss\TimeCountdown\Helper\ModuleConfig $helperProduct,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\CatalogRule\Model\RuleFactory $catalogRule,
        \Bss\TimeCountdown\Model\ResourceModel\ResourceProduct $resourceProduct,
        array $data = [])
    {
        $this->helperProduct=$helperProduct;
        $this->storeManager = $storeManager;
        $this->catalogRule = $catalogRule;
        $this->resourceProduct = $resourceProduct;
        parent::__construct($context,
            $productCollectionFactory,
            $catalogProductVisibility,
            $httpContext,
            $sqlBuilder,
            $rule,
            $conditionsHelper,
            $data
        );
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        if(!$this->isEnableWidgetListOnSale() || !$this->helperProduct->isEnableModuleTimeCountdown() ) {
            return '';
        }
        if(!$this->getTemplate()){
            $this->setTemplate("widget/bssproductlistonsale.phtml");
        }
        $this->setTemplate($this->getTemplate());

        return parent::_toHtml();
    }

    /**
     * @return $this
     */
    public function setCache(){
        return $this->setData('cache_lifetime', '0');
    }

    /**
     * @return $this|\Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function createCollection()
    {
        $this->setCache();
        $dateZone = $this->helperProduct->getDateTimeZone();
        $timeZone = strtotime($dateZone);

        $toTimeRule = $timeZone - 3600 * 24;
        $toDateRule = date('Y-m-d-H:i:s', $toTimeRule);

        $date = date('Y-m-d-H:i:s', $timeZone);
        $numDayEnd = $this->helperProduct->numDayEnd() - 1;
        $timeEnnd = $timeZone + 86400 * $numDayEnd;
        $dateEnd = date('Y-m-d-H:m:i', $timeEnnd);

        $productCollection = $this->productCollectionFactory->create();
        $productCollection
            ->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds())
            ->addFieldToFilter('special_to_date', ['lteq' => $dateEnd])
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect('*')
            ->addUrlRewrite()
            ->addStoreFilter()
            ->addAttributeToSort(
                'minimal_price',
                'asc'
            );
        $this->resourceProduct->getSelectProductOnsale($productCollection,'price_index.final_price < price_index.price');

        //get list product onsale by catalog rule
        $websiteId = $this->storeManager->getStore()->getWebsiteId();//current Website Id
        $resultProductIds = [];
        $rule_ids = [];

        $catalogRuleCollection = $this->catalogRule->create()->getCollection();
        $catalogRuleCollection
            ->addIsActiveFilter(1)
            ->addFieldToFilter('from_date',['lteq' => $date])
            ->addFieldToFilter('to_date',['gteq' => $toDateRule])
            ->addFieldToFilter('to_date',['lteq' => $dateEnd]);

        foreach ($catalogRuleCollection as $catalogRule) {
            $rule_ids[] = $catalogRule->getId();
            $productIdsAccToRule = $catalogRule->getMatchingProductIds();
            foreach ($productIdsAccToRule as $productId => $ruleProductArray) {
                if (!empty($ruleProductArray[$websiteId])) {
                    $resultProductIds[] = $productId;
                }
            }
        }

        //array product id onsale by special and catalog rule
        $arr = $productCollection->getAllIds();
        if($resultProductIds) {
            $arr = array_merge($arr,$resultProductIds);
        }

        //List product by array product id above
        $collection  = $this->productCollectionFactory->create();
        $collection
            ->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds())
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id',['in'=>$arr])
            ->addStoreFilter()
            ->setPageSize($this->getPageSize())
            ->setCurPage($this->getRequest()->getParam($this->getData('page_var_name'), 1));
        return $collection;
    }

    /**
     * @return string
     */
    public function getUniqueKey()
    {
        $key = uniqid();
        return $key;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPagerHtml()
    {
        if ($this->showPager() && $this->getProductCollection()->getSize() > $this->getProductsPerPage()) {
            if (!$this->pager) {
                $this->pager = $this->getLayout()->createBlock(
                    \Magento\Catalog\Block\Product\Widget\Html\Pager::class,
                    'widget.products.list.on.sale.pager' . $this->getUniqueKey()
                );

                $this->pager->setUseContainer(true)
                    ->setShowAmounts(false)
                    ->setShowPerPage(false)
                    ->setPageVarName($this->getData('page_var_name'))
                    ->setLimit($this->getProductsPerPage())
                    ->setTotalLimit($this->getProductsCount())
                    ->setCollection($this->getProductCollection());
            }
            if ($this->pager instanceof \Magento\Framework\View\Element\AbstractBlock) {
                return $this->pager->toHtml();
            }
        }
        return '';
    }

    /**
     * @return mixed
     */
    public function getTitleOnSale () {
        if (!$this->hasData('title_list_product_onsale')) {
            $this->setData('title_list_product_onsale', self::DEFAULT_TITLE_WIDGET);
        }
        return $this->getData('title_list_product_onsale');
    }

    /**
     * @return mixed
     */
    public function isEnableWidgetListOnSale () {
        if (!$this->hasData('enable_widget_list_onsale')) {
            $this->setData('enable_widget_list_onsale', self::DEFAULT_ENABLE_WIDGET_LIST);
        }
        return $this->getData('enable_widget_list_onsale');
    }

    /**
     * @return bool
     */
    public function show_slider() {
        if (!$this->hasData('show_slide_onsale')) {
            $this->setData('show_slide_onsale', self::DEFAULT_SHOW_SLIDE);
        }
        return (bool)$this->getData('show_slide_onsale');
    }


    /**
     * @return mixed
     */
    public function time_auto_slide() {
        if (!$this->hasData('time_auto_slide_onsale')) {
            $this->setData('time_auto_slide_onsale', self::DEFAULT_TIME_AUTO_SLIDE);
        }
        return $this->getData('time_auto_slide_onsale');
    }

    /**
     * @return mixed
     */
    public function productPerSlide() {
        if (!$this->hasData('products_per_slide_onsale')) {
            $this->setData('products_per_slide_onsale', self::DEFAULT_PRODUCT_PER_SLIDE);
        }
        return $this->getData('products_per_slide_onsale');
    }

    /**
     * @return int
     */
    protected function getPageSize()
    {
        if($this->show_slider()) {
            return $this->getProductsCount();
        }
        return $this->showPager() ? $this->getProductsPerPage() : $this->getProductsCount();
    }

}