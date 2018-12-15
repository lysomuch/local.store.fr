<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Feed
 */


namespace Amasty\Feed\Controller\Adminhtml\Feed;

use Amasty\Feed\Api\Data\ValidProductsInterface;

class Ajax extends \Amasty\Feed\Controller\Adminhtml\Feed
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var \Magento\Framework\UrlFactory
     */
    private $urlFactory;

    /**
     * @var \Amasty\Feed\Model\Config
     */
    private $config;

    /**
     * @var \Amasty\Feed\Model\Feed
     */
    private $feed;

    /**
     * @var \Amasty\Feed\Model\ResourceModel\Feed
     */
    private $feedResource;

    /**
     * @var \Amasty\Feed\Api\ValidProductsRepositoryInterface
     */
    private $validProductsRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $criteriaBuilder;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Psr\Log\LoggerInterface $logger,
        \Amasty\Feed\Model\RuleFactory $ruleFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\UrlFactory $urlFactory,
        \Amasty\Feed\Model\Config $config,
        \Amasty\Feed\Model\FeedFactory $feedFactory,
        \Amasty\Feed\Model\ResourceModel\Feed $feedResource,
        \Amasty\Feed\Api\ValidProductsRepositoryInterface $validProductsRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $criteriaBuilder
    ) {
        parent::__construct($context, $coreRegistry, $resultLayoutFactory, $logger, $ruleFactory);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->urlFactory = $urlFactory;
        $this->config = $config;
        $this->feed = $feedFactory->create();
        $this->feedResource = $feedResource;
        $this->validProductsRepository = $validProductsRepository;
        $this->criteriaBuilder = $criteriaBuilder;
    }

    /**
     * @return \Magento\Framework\UrlInterface
     */
    private function getUrlInstance()
    {
        return $this->urlFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $page = (int)$this->getRequest()->getParam('page', 0);
        $feedId = $this->getRequest()->getParam('feed_entity_id');
        $itemsPerPage = (int)$this->config->getItemsPerPage();
        $body = [];
        // Valid page for searchCriteria
        $currentPage = $page + 1;

        try {
            $lastPage = false;
            $this->feedResource->load($this->feed, $feedId, 'entity_id');
            /** @var \Magento\Framework\Api\SearchCriteria $searchCriteria */
            $searchCriteria = $this->criteriaBuilder->addFilter(
                ValidProductsInterface::FEED_ID,
                $feedId
            )
                ->setPageSize($itemsPerPage)
                ->setCurrentPage($currentPage)
                ->create();
            $validProducts = $this->validProductsRepository->getList($searchCriteria);
            $totalPages = ceil($validProducts->getTotalCount() / $itemsPerPage);

            if ((int)$page == $totalPages - 1 || $totalPages == 0) {
                $lastPage = true;
            }

            $productItems = $validProducts->getItems();
            $this->feed->export($page, $productItems, $lastPage);

            if ($lastPage) {
                $this->feed->compress();
            }

            $body['exported'] = count($productItems);
            $body['isLastPage'] = $lastPage;
            $body['total'] = $validProducts->getTotalCount();
        } catch (\RuntimeException $e) {
            $body['error'] = $e->getMessage();
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $body['error'] = __('Something went wrong while export feed data. Please review the error log.');
        }

        if (!isset($body['error'])) {
            $urlInstance = $this->getUrlInstance();

            $routeParams = [
                '_direct' => 'amfeed/feed/download',
                '_query' => [
                    'filename' => $this->feed->getData('filename')
                ]
            ];

            $href = $urlInstance
                ->setScope($this->feed->getStoreId())
                ->getUrl(
                    '',
                    $routeParams
                );

            $body['download'] = $href;
        }

        return $this->resultJsonFactory->create()->setData($body);
    }
}
