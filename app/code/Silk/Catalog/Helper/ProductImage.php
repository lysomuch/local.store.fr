<?php
/**
 * Created by PhpStorm.
 * User: cristen
 * Date: 2018/6/14
 * Time: 16:37
 */

namespace Silk\Catalog\Helper;


class ProductImage
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /** @var \Magento\Catalog\Model\ProductRepository */
    protected $_productRepository;

    /** @var \Magento\Catalog\Helper\Image */
    protected $imageHelper;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Helper\ImageFactory $imageHelperFactory
    ) {
        $this->_storeManager = $storeManager;
        $this->_productRepository = $productRepository;
        $this->imageHelper = $imageHelperFactory->create();
    }

    /**
     * 获取产品缩略图
     * @param \Magento\Catalog\Model\Product $product
     * @return mixed
     */
    public function getProductImage(\Magento\Catalog\Model\Product $product)
    {
        $productImage = $this->imageHelper->init($product, 'category_page_list')
            ->constrainOnly(false)
            ->keepAspectRatio(true)
            ->keepFrame(false)
            ->resize(70, 70)
            ->getUrl();
        return $productImage;
    }

    /**
     * 通过产品ID获取产品缩略图
     * @param int $product_id
     * @param string $image_id 可选值为主题/etc/view.xml中image标签id属性值
     * @param string $placeholder_type 加载不到产品时默认图片，可选值['image', 'smal_image', 'swatch_image','thumbnail']
     * @return string
     */
    public function getImageUrlById($product_id=0, $image_id='product_small_image', $placeholder_type='smal_image')
    {
        $storeId = $this->_storeManager->getStore()->getId();

        try {
            $_product = $this->_productRepository->getById($product_id, FALSE, $storeId);

            return $this->imageHelper->init($_product, $image_id)->getUrl();
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e){
            return $this->imageHelper->getDefaultPlaceholderUrl($placeholder_type);
        }
    }
}