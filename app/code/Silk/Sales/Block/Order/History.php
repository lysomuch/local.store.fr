<?php
/**
 * Created by PhpStorm.
 * User: cristen
 * Date: 2018/5/18
 * Time: 10:40
 */
namespace Silk\Sales\Block\Order;

class History extends \Magento\Sales\Block\Order\History
{
    protected $_template = 'Magento_Sales::order/history.phtml';

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $productHelper;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /** @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface */
    protected $timezone;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        array $data = [],
        \Magento\Catalog\Helper\ImageFactory $imageHelperFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {
        parent::__construct($context, $orderCollectionFactory, $customerSession, $orderConfig, $data);
        $this->imageHelper = $imageHelperFactory->create();
        $this->_productRepository = $productRepository;
        $this->productHelper = $productHelper;
        $this->timezone = $timezone;
    }

    /**
     * 获取产品缩略图
     * @param int $product_id
     * @param string $image_id 可选值为主题/etc/view.xml中image标签id属性值
     * @return string
     */
    public function getImageUrl($product_id=0, $image_id='product_small_image')
    {
        $storeId = $this->_storeManager->getStore()->getId();

        try {
            $_product = $this->_productRepository->getById($product_id, FALSE, $storeId);
            return $this->imageHelper->init($_product, $image_id)->getUrl();
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e){
            return $this->imageHelper->getDefaultPlaceholderUrl('small_image');
        }
    }

    /**
     * 获取产品详情页链接
     * @param int $product_id
     * @return string
     */
    public function getProductUrl($product_id=0)
    {
        $storeId = $this->_storeManager->getStore()->getId();

        try {
            $_product = $this->_productRepository->getById($product_id, FALSE, $storeId);
            return $this->productHelper->getProductUrl($_product);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e){
            return 'javascript:void(0);';
        }
    }

    /**
     * 格式化日期时间
     * @param $origin_date 原始时间
     * @return String
     */
    public function formatDateTime($origin_date) {
        return $this->timezone->date(new \DateTime($origin_date))->format('Y-n-j H:i');
    }
}