<?php

/**
 * User: Bob song <song01140228@163.com>
 * @date 18-7-18 下午1:51
 */

namespace Silk\Kingdee\Model;

class ProductStock implements \Silk\Kingdee\Api\ProductStockInterface
{
    protected $_productFactory;

    protected $_stockRegistry;

    /**
     * ProductStock constructor.
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\CatalogInventory\Model\StockRegistry $stockRegistry
     */
    public function __construct
    (
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\CatalogInventory\Model\StockRegistry $stockRegistry
     )
    {
        $this->_productFactory = $productFactory;
        $this->_stockRegistry = $stockRegistry;
    }

    /**
     * get products stock
     *
     * @api
     * @return mixed
     */
    public function getAllProductStock()
    {
        $stockArr = array();
        $productModel = $this->_productFactory->create();
        $collection = $productModel->getCollection()->addFieldToFilter('type_id', 'simple');
        /** @var  $product \Magento\Catalog\Model\Product */
        foreach ($collection as $item) {
            $data = array();
            $product = $this->_productFactory->create()->load($item->getId());
            if ($product->getId()) {
                $stock = $this->_stockRegistry->getStockItem($product->getId());
//                $data['ProductId'] = $product->getId();
                $data['Sku'] = $product->getSku();
                $data['Name'] = $product->getName();
                $data['MaterialCode'] = $product->getMaterialCode();
                $data['Qty'] = $stock->getQty();
                $stockArr['stock'][] = $data;
            }
        }
        $result = ['code' => 200, 'massage' => 'Success', 'data' => $stockArr];
        return json_encode($result);

    }
}