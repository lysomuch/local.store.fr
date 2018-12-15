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
 * @package    Bss_ProductGridInlineEditor
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\ProductGridInlineEditor\Controller\Adminhtml;

abstract class InlineEditor extends \Magento\Backend\App\Action
{   
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Helper\Product\Edit\Action\Attribute
     */
    protected $attributeHelper;

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $catalogProduct;

    /**
     * @var \Magento\Catalog\Model\Product\Action
     */
    protected $productAction;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Flat\Processor
     */
    protected $productFlatIndexerProcessor;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Price\Processor
     */
    protected $productPriceIndexerProcessor;

    /**
     * @var \Magento\CatalogInventory\Model\Indexer\Stock\Processor
     */
    protected $stockIndexerProcessor;
    
    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterfaceFactory
     */
    protected $stockRegistryFactory;

    /**
     * @var \Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory
     */
    protected $stockItemFactory;

    /**
     * @var \Magento\CatalogInventory\Api\StockItemRepositoryInterfaceFactory
     */
    protected $stockItemRepositoryFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productloader;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;


    /**
     * InlineEdit constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Helper\Product\Edit\Action\Attribute $attributeHelper
     * @param \Magento\Catalog\Helper\Product $catalogProduct
     * @param \Magento\Catalog\Model\Product\Action $productAction
     * @param \Magento\Catalog\Model\Indexer\Product\Flat\Processor $productFlatIndexerProcessor
     * @param \Magento\Catalog\Model\Indexer\Product\Price\Processor $productPriceIndexerProcessor
     * @param \Magento\CatalogInventory\Model\Indexer\Stock\Processor $stockIndexerProcessor
     * @param \Magento\CatalogInventory\Api\StockRegistryInterfaceFactory $stockRegistryFactory
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory $stockItemFactory
     * @param \Magento\CatalogInventory\Api\StockItemRepositoryInterfaceFactory $stockItemRepositoryFactory
     * @param \Magento\Catalog\Model\ProductFactory $productloader
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Helper\Product\Edit\Action\Attribute $attributeHelper,
        \Magento\Catalog\Helper\Product $catalogProduct,
        \Magento\Catalog\Model\Product\Action $productAction,
        \Magento\Catalog\Model\Indexer\Product\Flat\Processor $productFlatIndexerProcessor,
        \Magento\Catalog\Model\Indexer\Product\Price\Processor $productPriceIndexerProcessor,
        \Magento\CatalogInventory\Model\Indexer\Stock\Processor $stockIndexerProcessor,
        \Magento\CatalogInventory\Api\StockRegistryInterfaceFactory $stockRegistryFactory,
        \Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory $stockItemFactory,
        \Magento\CatalogInventory\Api\StockItemRepositoryInterfaceFactory $stockItemRepositoryFactory,
        \Magento\Catalog\Model\ProductFactory $productloader,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->eavConfig = $eavConfig;
        $this->storeManager = $storeManager;
        $this->attributeHelper = $attributeHelper;
        $this->catalogProduct = $catalogProduct;
        $this->productAction = $productAction;
        $this->productFlatIndexerProcessor = $productFlatIndexerProcessor;
        $this->productPriceIndexerProcessor = $productPriceIndexerProcessor;
        $this->stockIndexerProcessor = $stockIndexerProcessor;
        $this->stockRegistryFactory = $stockRegistryFactory;
        $this->stockItemFactory = $stockItemFactory;
        $this->stockItemRepositoryFactory = $stockItemRepositoryFactory;
        $this->productloader = $productloader;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function updateInventory()
    {
        $postItems  = $this->getRequest()->getParam('items', []);
        $productIds = array_keys($postItems);
        $storeId    = $this->getStoreId();
        $data = [];

        $stockRegistry = $this->stockRegistryFactory->create();
        $stockItemRepository = $this->stockItemRepositoryFactory->create();
        foreach ($productIds as $productId) {
            $inventoryData = [];
            $data = $postItems[$productId];
            if (isset($data['qty']) && $data['qty'] !='') {
                $inventoryData['qty'] = $data['qty'];
                $stockItemDo = $stockRegistry->getStockItem(
                    $productId,
                    $this->attributeHelper->getStoreWebsiteId($storeId)
                );
                if (!$stockItemDo->getProductId()) {
                    $inventoryData['product_id'] = $productId;
                }
                
                $stockItemId = $stockItemDo->getId();
                $this->dataObjectHelper->populateWithArray(
                    $stockItemDo,
                    $inventoryData,
                    \Magento\CatalogInventory\Api\Data\StockItemInterface::class
                );
                $stockItemDo->setItemId($stockItemId);
                $stock = $this->_saveStock($stockItemRepository, $stockItemDo);
            }
        }
    }

    /**
     * @param $stockItemRepository
     * @param $stockItemDo
     * @return mixed
     */
    protected function _saveStock($stockItemRepository, $stockItemDo)
    {   
        return $stockItemRepository->save($stockItemDo);
    }

    /**
     * @param $product
     * @param $newSku
     * @return mixed
     */
    protected function _saveSku($product, $newSku)
    {   
        $product->setStoreId(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
        $product->setSku($newSku)->save();
        return $product;
    }


    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getAttributesData()
    {
        $postItems = $this->getRequest()->getParam('items', []);
        $productIds = array_keys($postItems);
        $products_attributesData = $attributesData = [];;
        foreach ($productIds as $productId) {
            $attributesData = $postItems[$productId];
            foreach ($attributesData as $attributeCode => $value) {
                $attribute = $this->eavConfig->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $attributeCode);
                if (!$attribute->getAttributeId() || $attributeCode == 'sku') {
                    unset($attributesData[$attributeCode]);
                    continue;
                }
                if ($attribute->getBackendType() == 'datetime') {
                    if (!empty($value)) {
                        $value = date("Y-m-d",strtotime($value));
                    } else {
                        $value = null;
                    }
                    $attributesData[$attributeCode] = $value;
                } elseif ($attribute->getFrontendInput() == 'multiselect') {
                    if (is_array($value)) {
                        $value = implode(',', $value);
                    }
                    $attributesData[$attributeCode] = $value;
                }
            }
            $products_attributesData[$productId] = $attributesData;
        }
        return $products_attributesData;
    }

    /**
     * @return array
     */
    protected function getSkus()
    {
        $postItems = $this->getRequest()->getParam('items', []);
        $productIds = array_keys($postItems);
        $product_skus = [];
        foreach ($productIds as $productId) {
            $attributesData = $postItems[$productId];
            foreach ($attributesData as $attributeCode => $value) {
                if ($attributeCode == 'sku') {
                    $product_skus[$productId] = $value;
                }
            }
        }
        return $product_skus;
    }

    /**
     * @param $product
     * @return \Magento\Catalog\Model\Product
     */
    protected function getLoadProduct($productId)
    {
        return $this->productloader->create()->load($productId);
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getStoreId()
    {
        $storeId = $this->getRequest()->getParam('store_id');
        if (!$storeId || $storeId == '') {
            $storeId = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
        }
        return (int)$storeId;
    }
}
