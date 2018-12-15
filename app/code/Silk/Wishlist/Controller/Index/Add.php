<?php
/**
 * 重定向直接返回到前一个页面 或者 返回JSON数据
 * Created by PhpStorm.
 * User: cristen
 * Date: 2018/7/2
 * Time: 14:14
 */

namespace Silk\Wishlist\Controller\Index;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\MultipleWishlist\Model\WishlistEditor;
use Magento\Wishlist\Controller\WishlistProviderInterface;

class Add extends \Magento\MultipleWishlist\Controller\Index\Add
{
    /** @var \Magento\Framework\App\Cache */
    protected $cache;

    public function __construct(
        Action\Context $context,
        Session $customerSession,
        WishlistProviderInterface $wishlistProvider,
        ProductRepositoryInterface $productRepository,
        Validator $formKeyValidator,
        WishlistEditor $wishlistEditor,
        \Magento\Framework\App\Cache $cache
    ) {
        $this->cache = $cache;

        parent::__construct(
            $context,
            $customerSession,
            $wishlistProvider,
            $productRepository,
            $formKeyValidator,
            $wishlistEditor
        );
    }

    /**
     * 维护该客户心愿商品id列表缓存 并 返回到前一页 或者 返回JSON结果
     * @param \Magento\MultipleWishlist\Controller\Index\Add $subject
     * @param $procede
     * @return mixed
     */
    public function aroundExecute($subject, $procede)
    {
        /* check is ajax request or not */
        $isAjax = $this->getRequest()->isXmlHttpRequest();

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        if ( ! $this->formKeyValidator->validate($this->getRequest())) {
            if($isAjax) {
                return $resultJson->setData(['code'=>400, 'msg'=>'We can\'t add the item to Wish List right now.']);
            }

            return $resultRedirect->setRefererUrl();
        }

        $customerId = $this->_customerSession->getCustomerId();
        $name = $this->getRequest()->getParam('name');
        $visibility = $this->getRequest()->getParam('visibility', 0) === 'on' ? 1 : 0;
        if ($name !== null) {
            try {
                $wishlist = $this->wishlistEditor->edit($customerId, $name, $visibility);

                $msg = __(
                    'Wish list "%1" was saved.',
                    $this->_objectManager->get(\Magento\Framework\Escaper::class)->escapeHtml($wishlist->getName())
                );

                if($isAjax) {
                    return $resultJson->setData(['code'=>200, 'msg'=>$msg]);
                }

                $this->messageManager->addSuccessMessage($msg);
                $this->getRequest()->setParam('wishlist_id', $wishlist->getId());
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                if($isAjax) {
                    return $resultJson->setData(['code'=>400, 'msg'=>$e->getMessage()]);
                }

                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $msg = __('We can\'t create the wish list right now.');

                if($isAjax) {
                    return $resultJson->setData(['code'=>400, 'msg'=>$msg]);
                }

                $this->messageManager->addExceptionMessage($e, $msg);
            }
        }

        $wishlist = $this->wishlistProvider->getWishlist();
        if ( ! $wishlist) {
            $msg = __('Page not found.');

            if($isAjax) {
                return $resultJson->setData(['code'=>400, 'msg'=>$msg]);
            }

            throw new NotFoundException($msg);
        }

        $session = $this->_customerSession;
        $requestParams = $this->getRequest()->getParams();

        if ($session->getBeforeWishlistRequest()) {
            $requestParams = $session->getBeforeWishlistRequest();
            $session->unsBeforeWishlistRequest();
        }

        $productId = isset($requestParams['product']) ? (int)$requestParams['product'] : null;
        if ( ! $productId) {
            if($isAjax) {
                return $resultJson->setData(['code'=>400, 'msg'=>'We can\'t add the item to Wish List right now.']);
            }

            return $resultRedirect->setRefererUrl();
        }

        try {
            $product = $this->productRepository->getById($productId);
        } catch (NoSuchEntityException $e) {
            $product = null;
        }

        if ( ! $product || ! $product->isVisibleInCatalog()) {
            $msg = __('We can\'t specify a product.');

            if($isAjax) {
                return $resultJson->setData(['code'=>400, 'msg'=>$msg]);
            }

            $this->messageManager->addErrorMessage($msg);
            return $resultRedirect->setRefererUrl();
        }

        try {
            $buyRequest = new \Magento\Framework\DataObject($requestParams);

            $result = $wishlist->addNewItem($product, $buyRequest);
            if (is_string($result)) {
                if($isAjax) {
                    return $resultJson->setData(['code'=>400, 'msg'=>__($result)]);
                }

                throw new \Magento\Framework\Exception\LocalizedException(__($result));
            }
            $wishlist->save();

            $this->_eventManager->dispatch(
                'wishlist_add_product',
                ['wishlist' => $wishlist, 'product' => $product, 'item' => $result]
            );

            $referer = $session->getBeforeWishlistUrl();
            if ($referer) {
                $session->setBeforeWishlistUrl(null);
            } else {
                $referer = $this->_redirect->getRefererUrl();
            }

            $this->_objectManager->get(\Magento\Wishlist\Helper\Data::class)->calculate();

            if( ! $isAjax) {
                $this->messageManager->addComplexSuccessMessage(
                    'addProductSuccessMessage',
                    [
                        'product_name' => $product->getName(),
                        'referer' => $referer
                    ]
                );
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $msg = __('We can\'t add the item to Wish List right now: %1.', $e->getMessage());

            if($isAjax) {
                return $resultJson->setData(['code'=>400, 'msg'=>$msg]);
            }

            $this->messageManager->addErrorMessage($msg);
            return $resultRedirect->setRefererUrl();
        } catch (\Exception $e) {
            $msg = __('We can\'t add the item to Wish List right now.');

            if($isAjax) {
                return $resultJson->setData(['code'=>400, 'msg'=>$msg]);
            }

            $this->messageManager->addExceptionMessage($msg);
            return $resultRedirect->setRefererUrl();
        }

        $cacheKey = 'getWishIdListByCustomerId_' . $customerId;
        //获取该用户心愿单列表
        $list = [];
        if ($wishlist) {
            $wishlistItems = $wishlist->getItemCollection();
            foreach($wishlistItems as $item) {
                $list[] = $item->getdata('product_id');
            }
        }

        //保存缓存
        $jsonData = json_encode($list);
        $this->cache->save($jsonData, $cacheKey, [], 3600);

        if($isAjax) {
            return $resultJson->setData(['code'=>200, 'msg'=>'added to your Wish List success.']);
        }

        return $resultRedirect->setRefererUrl();
    }
}
