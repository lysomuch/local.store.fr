<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Feed
 */


namespace Amasty\Feed\Cron;

use Amasty\Feed\Api\Data\ValidProductsInterface;
use Amasty\Feed\Model\Feed;
use Magento\Framework\App\ResourceConnection;

class RefreshData
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var \Amasty\Feed\Model\ResourceModel\Feed\CollectionFactory
     */
    private $feedCollectionFactory;

    /**
     * @var \Amasty\Feed\Model\Config
     */
    private $config;

    /**
     * @var \Amasty\Feed\Api\ValidProductsRepositoryInterface
     */
    private $validProductsRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $criteriaBuilder;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ResourceConnection $resource,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Psr\Log\LoggerInterface $logger,
        \Amasty\Feed\Model\ResourceModel\Feed\CollectionFactory $feedCollectionFactory,
        \Amasty\Feed\Model\Config $config,
        \Amasty\Feed\Api\ValidProductsRepositoryInterface $validProductsRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $criteriaBuilder
    ) {
        $this->_storeManager = $storeManager;
        $this->_resource = $resource;
        $this->_dateTime = $dateTime;
        $this->_localeDate = $localeDate;
        $this->logger = $logger;
        $this->feedCollectionFactory = $feedCollectionFactory;
        $this->config = $config;
        $this->validProductsRepository = $validProductsRepository;
        $this->criteriaBuilder = $criteriaBuilder;
    }

    public function execute()
    {
        $itemsPerPage = (int)$this->config->getItemsPerPage();
        /** @var \Amasty\Feed\Model\ResourceModel\Feed\Collection $collection */
        $collection = $this->feedCollectionFactory->create();

        /** @var Feed $feed */
        foreach ($collection as $feed) {
            try {
                if ($this->_onSchedule($feed)) {
                    $page = 0;
                    $lastPage = false;
                    /** @var \Magento\Framework\Api\SearchCriteria $searchCriteria */
                    $searchCriteria = $this->criteriaBuilder->addFilter(
                        ValidProductsInterface::FEED_ID,
                        $feed->getId()
                    )
                        ->setPageSize($itemsPerPage)
                        ->setCurrentPage($page)
                        ->create();
                    $validProducts = $this->validProductsRepository->getList($searchCriteria);
                    $totalPages = ceil($validProducts->getTotalCount() / $itemsPerPage);

                    while ($totalPages > $page) {
                        if ($page == $totalPages - 1) {
                            $lastPage = true;
                        }
                        $feed->export($page, $validProducts->getItems(), $lastPage);
                        $page++;
                    }
                }
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        }
    }

    protected function _validateTime($feed)
    {
        $validate = true;
        $cronTime = $feed->getCronTime();

        if (!empty($cronTime)) {
            $mageTime = $this->_localeDate->scopeTimeStamp();

            $validate = false;

            $times = explode(",", $cronTime);

            $now = (date("H", $mageTime) * 60) + date("i", $mageTime);

            foreach ($times as $time) {
                if ($now >= $time && $now < $time + 30) {
                    $validate = true;
                    break;
                }
            }
        }

        return $validate;
    }

    protected function _onSchedule($feed)
    {
        $threshold = 24; // Daily

        switch ($feed->getExecuteMode()) {
            case 'weekly':
                $threshold = 168;
                break;
            case 'monthly':
                $threshold = 5040;
                break;
            case 'hourly':
                $threshold = 1;
                break;
        }

        if ($feed->getExecuteMode() != 'manual'
            && $threshold <= (strtotime('now') - strtotime($feed->getGeneratedAt())) / 3600
            && $this->_validateTime($feed)
        ) {
            return true;
        }
        
        return false;
    }
}
