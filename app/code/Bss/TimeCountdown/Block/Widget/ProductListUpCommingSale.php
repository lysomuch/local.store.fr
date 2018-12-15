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
class ProductListUpCommingSale extends \Magento\CatalogWidget\Block\Product\ProductsList
{

    /**
     * @var \Bss\TimeCountdown\Helper\ModuleConfig
     */
    protected $helperProduct;

    /**
     * @var \Bss\TimeCountdown\Helper\ModuleConfig
     */
    protected $helperConfig;
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

    const DEFAULT_PRODUCT_PER_SLIDE = 5;

    const DEFAULT_TIME_AUTO_SLIDE = 0;

    /**
     * ProductListUpCommingSale constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder
     * @param \Magento\CatalogWidget\Model\Rule $rule
     * @param \Magento\Widget\Helper\Conditions $conditionsHelper
     * @param \Bss\TimeCountdown\Helper\ModuleConfig $helperProduct
     * @param \Bss\TimeCountdown\Helper\ModuleConfig $helperConfig
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
        \Bss\TimeCountdown\Helper\ModuleConfig $helperConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\CatalogRule\Model\RuleFactory $catalogRule,
        \Bss\TimeCountdown\Model\ResourceModel\ResourceProduct $resourceProduct,
        array $data = [])
    {
        $this->helperProduct=$helperProduct;
        $this->helperConfig=$helperConfig;
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
     * Set template
     * @return string
     */
    public function _toHtml()
    {
        if(!$this->isEnableWidgetListCommingSale() || !$this->helperConfig->isEnableModuleTimeCountdown()) {
            return '';
        }
        if(!$this->getTemplate()){ 
            $this->setTemplate("widget/bssproductlistcommingsale.phtml");
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
        $dateZone = $this->helperConfig->getDateTimeZone();
        $timeZone = strtotime($dateZone);

        $toTimeRule = $timeZone - 3600 * 24;
        $toDateRule = date('Y-m-d-H:i:s', $toTimeRule);

        $date = date('Y-m-d-H:i:s', $timeZone);
        $numDayStart = $this->helperConfig->numDayStart();
        $timeStart = $timeZone + 86400 * $numDayStart;
        $dateStart = date('Y-m-d-H:m:i', $timeStart);

        $numDayEnd = $this->helperConfig->numDayEnd() - 1;
        $timeEnnd = $timeZone + 86400 * $numDayEnd;
        $dateEnd = date('Y-m-d-H:m:i', $timeEnnd);

        //Listproduct upcomming sale (special price)
        $productCommingSaleCollection = $this->productCollectionFactory->create();

        $productCommingSaleCollection
            ->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds())

            ->addFieldToFilter('special_from_date', ['lteq' => $dateStart])
            ->addFieldToFilter('special_from_date', ['gteq' => $date])
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

        //List product onsale special price:
        $productOnsaleCollection = $this->productCollectionFactory->create();
        $productOnsaleCollection
            ->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds())
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
        $this->resourceProduct->getSelectProductOnsale($productOnsaleCollection,'price_index.final_price < price_index.price');

        $productIdsOnsale = $productOnsaleCollection->getAllIds();


        //get list catalog rule onsale
        $websiteId = $this->storeManager->getStore()->getWebsiteId();//current Website Id
        $resultProductIdsOnsale = [];
        $rule_ids = [];
        $catalogRuleOnsaleCollection = $this->catalogRule->create()->getCollection();
        $catalogRuleOnsaleCollection
            ->addIsActiveFilter(1)
            ->addFieldToFilter('from_date',['lteq' => $date])
            ->addFieldToFilter('to_date',['gteq' => $toDateRule]);

        foreach ($catalogRuleOnsaleCollection as $catalogRuleOnsale) {
            $rule_ids[] = $catalogRuleOnsale->getId();
            $productIdsAccToRule = $catalogRuleOnsale->getMatchingProductIds();
            foreach ($productIdsAccToRule as $productId => $ruleProductArray) {
                if (!empty($ruleProductArray[$websiteId])) {
                    $resultProductIdsOnsale[] = $productId;
                }
            }
        }

        $arrIdOnsale = array_merge($productIdsOnsale,$resultProductIdsOnsale);


        //get list catalog rule upcomming sale
        $resultProductIdsCommingsale = [];
        $rule_ids = [];
        $catalogRuleCommingsaleCollection = $this->catalogRule->create()->getCollection();
        $catalogRuleCommingsaleCollection
            ->addIsActiveFilter(1)
            ->addFieldToFilter('from_date', ['lteq' => $dateStart])
            ->addFieldToFilter('from_date', ['gteq' => $date]);

        foreach ($catalogRuleCommingsaleCollection as $catalogRuleCommingsale) {
            $rule_ids[] = $catalogRuleCommingsale->getId();
            $productIdsAccToRule = $catalogRuleCommingsale->getMatchingProductIds();
            foreach ($productIdsAccToRule as $productId => $ruleProductArray) {
                if (!empty($ruleProductArray[$websiteId])) {
                    $resultProductIdsCommingsale[] = $productId;
                }
            }
        }
        $productIdsCommingsale = $productCommingSaleCollection->getAllIds();

        $arrIds = array_merge($productIdsCommingsale,$resultProductIdsCommingsale);

        //List product by array product id above
        $collection  = $this->productCollectionFactory->create();
        $collection
            ->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds())
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id',['in'=>$arrIds]);

            if($arrIdOnsale) {
                $collection->addAttributeToFilter('entity_id',['nin'=>$arrIdOnsale]);
            }

        $collection->addStoreFilter()
            ->setPageSize($this->getPageSize())
            ->setCurPage($this->getRequest()->getParam($this->getData('page_var_name'), 1));
        return $collection;
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
                    'widget.products.list.comming.sale.pager' . $this->getUniqueKey()
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
    public function getTitleCommingSale () {
        if (!$this->hasData('title_list_product_comming_sale')) {
            $this->setData('title_list_product_comming_sale', self::DEFAULT_TITLE_WIDGET);
        }
        return $this->getData('title_list_product_comming_sale');
    }

    /**
     * @return mixed
     */
    public function isEnableWidgetListCommingSale () {
        if (!$this->hasData('enable_widget_list_comming_sale')) {
            $this->setData('enable_widget_list_comming_sale', self::DEFAULT_ENABLE_WIDGET_LIST);
        }
        return $this->getData('enable_widget_list_comming_sale');
    }

    /**
     * @return bool
     */
    public function show_slider() {
         if (!$this->hasData('show_slide')) {
            $this->setData('show_slide', self::DEFAULT_SHOW_SLIDE);
        }
        return (bool)$this->getData('show_slide');
    }

    /**
     * @return mixed
     */
    public function productPerSlide() {
        if (!$this->hasData('products_per_slide')) {
            $this->setData('products_per_slide', self::DEFAULT_PRODUCT_PER_SLIDE);
        }
        return $this->getData('products_per_slide');
    }

    /**
     * @return mixed
     */
    public function time_auto_slide() {
        if (!$this->hasData('time_auto_slide')) {
            $this->setData('time_auto_slide', self::DEFAULT_TIME_AUTO_SLIDE);
        }
        return $this->getData('time_auto_slide');
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
