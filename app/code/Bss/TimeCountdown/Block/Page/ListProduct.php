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
namespace Bss\TimeCountdown\Block\Page;

class ListProduct extends \Magento\Catalog\Block\Product\ListProduct {

    const DEFAULT_SHOW_PAGER = false;

    const DEFAULT_PRODUCTS_PER_PAGE = 5;

    const DEFAULT_PRODUCTS_COUNT = 7;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Bss\TimeCountdown\Helper\ModuleConfig
     */
    protected $helperConfig;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $catalogProductVisibility;
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

    /**
     * ListProduct constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Bss\TimeCountdown\Helper\ModuleConfig $helperConfig
     * @param \Magento\Framework\View\Page\Config $pageConfig
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\CatalogRule\Model\RuleFactory $catalogRule
     * @param \Bss\TimeCountdown\Model\ResourceModel\ResourceProduct $resourceProduct
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Bss\TimeCountdown\Helper\ModuleConfig $helperConfig,
        \Magento\Framework\View\Page\Config $pageConfig,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\CatalogRule\Model\RuleFactory $catalogRule,
        \Bss\TimeCountdown\Model\ResourceModel\ResourceProduct $resourceProduct,
        array $data = []
    )
    {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->helperConfig=$helperConfig;
        $this->pageConfig = $pageConfig;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->storeManager = $storeManager;
        $this->catalogRule = $catalogRule;
        $this->resourceProduct = $resourceProduct;
        parent::__construct($context,$postDataHelper,$layerResolver,$categoryRepository,$urlHelper, $data);
    }

    /**
     * @return \Magento\Catalog\Model\Product|\Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getproduct() {
        $page=($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
        $pageSize=($this->getRequest()->getParam('limit'))? $this->getRequest()->getParam('limit') : 5;

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


        $productOnsaleCollection = $this->productCollectionFactory->create();

        //List product onsale special depend todate
        $productOnsaleCollection
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

        $this->resourceProduct->getSelectProductOnsale($productOnsaleCollection,'price_index.final_price < price_index.price');
        $productIdsOnsale = $productOnsaleCollection->getAllIds();

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
        $productIdsCommingsale = $productCommingSaleCollection->getAllIds();
        $arrIds = array_merge($productIdsOnsale,$productIdsCommingsale);


        $websiteId = $this->storeManager->getStore()->getWebsiteId();//current Website Id

        //get list catalog rule onsale
        $resultProductIdsOnsale = [];
        $rule_ids = [];
        $catalogRuleOnsaleCollection = $this->catalogRule->create()->getCollection();
        $catalogRuleOnsaleCollection
            ->addIsActiveFilter(1)
            ->addFieldToFilter('from_date',['lteq' => $date])
            ->addFieldToFilter('to_date',['gteq' => $toDateRule])
            ->addFieldToFilter('to_date',['lteq' => $dateEnd]);

        foreach ($catalogRuleOnsaleCollection as $catalogRuleOnsale) {
            $rule_ids[] = $catalogRuleOnsale->getId();
            $productIdsAccToRule = $catalogRuleOnsale->getMatchingProductIds();
            foreach ($productIdsAccToRule as $productId => $ruleProductArray) {
                if (!empty($ruleProductArray[$websiteId])) {
                    $resultProductIdsOnsale[] = $productId;
                }
            }
        }

        if($resultProductIdsOnsale) {
            $arrIds = array_merge($arrIds,$resultProductIdsOnsale);
        }

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

        if($resultProductIdsCommingsale) {
            $arrIds = array_merge($arrIds,$resultProductIdsCommingsale);
        }

        //get list collection sale, comming sale by special price and catalog rule
        $collection  = $this->productCollectionFactory->create();
        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds())
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('entity_id',['in'=>$arrIds])
                    ->setPageSize($pageSize)
                    ->setCurPage($page);
        return $collection;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->pageConfig->addBodyClass('advance-sitemap');
        $titlePage = $this->helperConfig->getTitlePage();
        $metaTitle = $this->helperConfig->getMetaTitle();
        $metaKeyword = $this->helperConfig->getMetaKeyword();
        $metaDesc = $this->helperConfig->getMetaDesc();
        if($metaTitle) {
            $this->pageConfig->getTitle()->set($metaTitle);
        }

        if($metaKeyword) {
            $this->pageConfig->setKeywords($metaKeyword);
        }

        if($metaDesc) {
            $this->pageConfig->setDescription($metaDesc);
        }


        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle($titlePage);
        }


        if ($this->getproduct()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'bss.news.pager'
            )->setAvailableLimit([5=>5,10=>10,15=>15])->setShowPerPage(true)->setCollection(
                $this->getproduct()
            );
            $this->setChild('pager', $pager);
            $this->getproduct()->load();
        }
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

}
