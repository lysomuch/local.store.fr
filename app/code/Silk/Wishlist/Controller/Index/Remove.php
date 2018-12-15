<?php
/**
 * 重定向直接返回到前一个页面
 * Created by PhpStorm.
 * User: cristen
 * Date: 2018/7/2
 * Time: 14:14
 */

namespace Silk\Wishlist\Controller\Index;

class Remove
{
    /** @var \Magento\Wishlist\Controller\WishlistProviderInterface */
    protected $wishlistProvider;

    /** @var \Magento\Framework\App\Cache */
    protected $cache;

    /** @var \Magento\Customer\Model\Session $customerSession */
    protected $customerSession;

    public function __construct(
        \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider,
        \Magento\Framework\App\Cache $cache,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->wishlistProvider = $wishlistProvider;
        $this->cache = $cache;
        $this->customerSession = $customerSession;
    }

    /**
     * 维护该客户心愿商品id列表缓存
     * @param \Magento\Wishlist\Controller\Index\Add $subject
     * @param $result
     * @return mixed
     */
    public function afterExecute(\Magento\Wishlist\Controller\Index\Remove $subject, $result)
    {
        $customerId = $this->customerSession->getCustomer()->getId();
        if( ! $customerId) return $result;

        $cacheKey = 'getWishIdListByCustomerId_' . $customerId;
        //获取该用户心愿单列表
        $list = [];
        $currentUserWishlist = $this->wishlistProvider->getWishlist();
        if ($currentUserWishlist) {
            $wishlistItems = $currentUserWishlist->getItemCollection();
            foreach($wishlistItems as $item) {
                $list[] = $item->getdata('product_id');
            }
        }

        //保存缓存
        $jsonData = json_encode($list);
        $this->cache->save($jsonData, $cacheKey, [], 3600);

        return $result;
    }
}
