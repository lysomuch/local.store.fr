<?php
/**
 * Created by PhpStorm.
 * User: bob
 * Date: 18-5-8
 * Time: 下午4:13
 */

namespace Silk\Catalog\Block\Product;


class Accessories extends \Magento\Catalog\Block\Product\View
{
    /**
     * @return $this
     */
    public function getUpSellProduct()
    {
        $product = $this->getProduct();
        $upSells = $product->getUpSellProductCollection()->setPositionOrder()->addStoreFilter();

        $this->_addProductAttributesAndPrices($upSells);
        return $upSells;
    }


    public function getProductImage(\Magento\Catalog\Model\Product $product)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $imageHelper = $objectManager->get('Magento\Catalog\Helper\Image');
        $productImage = $imageHelper->init($product, 'category_page_list')
            ->constrainOnly(false)
            ->keepAspectRatio(true)
            ->keepFrame(false)
            ->resize(70, 70)
            ->getUrl();
        return $productImage;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getProductPrice(\Magento\Catalog\Model\Product $product)
    {
        $priceRender = $this->getPriceRender();

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product
            );
        }

        return $price;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getPriceRender()
    {
        return $this->getLayout()->getBlock('product.price.render.default');
    }
}