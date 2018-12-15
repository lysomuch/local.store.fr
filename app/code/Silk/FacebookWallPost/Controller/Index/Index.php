<?php
/**
 * Created by PhpStorm.
 * User: cristen
 * Date: 2018/7/3
 * Time: 10:01
 */

namespace Silk\FacebookWallPost\Controller\Index;
use Magento\Framework\App\ResponseInterface;

class Index extends \Magento\Framework\App\Action\Action
{
    /** @var \Magento\Framework\App\Cache */
    protected $cache;

    /** @var \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory */
    protected $jsonEncoder;

    /** @var \Silk\FacebookWallPost\Model\Source\WallPosts */
    protected $wallPostModel;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Cache $cache,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Silk\FacebookWallPost\Model\Source\WallPostsFactory $wallPostModelFactory
    ) {
        parent::__construct($context);
        $this->cache = $cache;
        $this->jsonEncoder = $resultJsonFactory;
        $this->wallPostModel = $wallPostModelFactory->create();
    }

    public function execute()
    {
        $resultJson = $this->jsonEncoder->create();

        $cacheKey = 'getFacebookWallPost';
        $jsonData = $this->cache->load($cacheKey);

        //没有缓存，需要重新获取
        if( ! $jsonData) {
            $list = [];
            $list['info'] = $this->wallPostModel->getPageInfo();
            $list['posts'] = $this->wallPostModel->getWallPosts();
            $jsonData = json_encode($list);

            //保存缓存
            $this->cache->save($jsonData, $cacheKey, [], 3600);
        }

        return $resultJson->setJsonData($jsonData);
    }
}