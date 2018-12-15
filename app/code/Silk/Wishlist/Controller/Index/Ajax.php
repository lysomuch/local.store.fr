<?php
/**
 * Created by PhpStorm.
 * User: cristen
 * Date: 2018/7/3
 * Time: 10:01
 */

namespace Silk\Wishlist\Controller\Index;


use Magento\Framework\App\ResponseInterface;

class Ajax extends \Magento\Framework\App\Action\Action
{
    /** @var \Magento\Wishlist\Controller\WishlistProviderInterface */
    protected $wishlistProvider;

    /** @var \Magento\Framework\App\Cache */
    protected $cache;

    /** @var \Magento\Customer\Model\Session $customerSession */
    protected $customerSession;

    /** @var \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory */
    protected $jsonEncoder;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider,
        \Magento\Framework\App\Cache $cache,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->wishlistProvider = $wishlistProvider;
        $this->cache = $cache;
        $this->customerSession = $customerSession;
        $this->jsonEncoder = $resultJsonFactory;
    }

    /**
     * 获取心愿单ID列表
     * @return array
     */
    public function execute()
    {
        $resultJson = $this->jsonEncoder->create();

        $customerId = $this->customerSession->getCustomer()->getId();
        if( ! $customerId) {
            return $resultJson->setJsonData(json_encode([]));
        }

        $cacheKey = 'getWishIdListByCustomerId_' . $customerId;
        $jsonData = $this->cache->load($cacheKey);

        //没有缓存，需要从数据库获取
        if( ! $jsonData) {
            $list = [];
            $currentUserWishlist = $this->wishlistProvider->getWishlist();
            if ($currentUserWishlist) {
                $wishlistItems = $currentUserWishlist->getItemCollection();
                foreach($wishlistItems as $item) {
                    $list[] = $item->getdata('product_id');
                }
            };
            $jsonData = json_encode($list);

            //保存缓存
            $this->cache->save($jsonData, $cacheKey, [], 3600);
        }


        return $resultJson->setJsonData($jsonData);
    }
}