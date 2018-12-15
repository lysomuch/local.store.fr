<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Silk\Review\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
/**
 * Class GreetingCommand
 */
class Review extends Command
{
    protected $fileName = 'var/import_file/review.csv';

    protected $csv;

    protected $reviewFactory;

    protected $ratingFactory;

    protected $customerSession;

    protected $storeId = 1;

    protected $defaultTitleLen = 30;

    protected $_productFactory;

    protected $ratingCode = [4 => '20',5 => '25',6 => '30'];

    protected $isReviewRating = true;

    public function __construct(
        \Magento\Framework\File\Csv $csv,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Review\Model\RatingFactory $ratingFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        $name = null
    )
    {
        $this->reviewFactory = $reviewFactory;
        $this->ratingFactory = $ratingFactory;
        $this->_productFactory = $productFactory;
        $this->csv = $csv;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('shell:import_review')->setDescription('Import product review');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (true) {
            $bridalCsv = clone $this->csv;
            $fileData = $bridalCsv->getData($this->fileName);
            $title = $fileData[0];
            unset($fileData[0]);
            foreach ($fileData as $item) {
                $data = array_combine($title, $item);
                if (!$data['title']) {
                    if (strlen($data['detail']) > $this->defaultTitleLen) {
                        $data['title'] = substr($data['detail'], 0, $this->defaultTitleLen) . '...';
                    } else {
                        $data['title'] = $data['detail'];
                    }
                }
                $productModel = $this->_productFactory->create();
                $product = $productModel->loadByAttribute('sku', $data['sku']);
                if (!$product->getId()) {
                    continue;
                }
                $review = $this->reviewFactory->create()->setData($data);
                $review->unsetData('review_id');


                $review->setEntityId($review->getEntityIdByCode(\Magento\Review\Model\Review::ENTITY_PRODUCT_CODE))
                    ->setEntityPkValue($product->getId())
                    ->setStatusId(\Magento\Review\Model\Review::STATUS_APPROVED)
                    ->setCustomerId(null)
                    ->setStoreId($this->storeId)
                    ->setStores([$this->storeId])
                    ->save();
                $review->setCreatedAt($data['created_at'])->save();


                if ($this->isReviewRating) {
                    $rating = $this->getRatingData();
                    foreach ($rating as $ratingId => $optionId) {
                        $this->ratingFactory->create()
                            ->setRatingId($ratingId)
                            ->setReviewId($review->getId())
                            ->setCustomerId(null)
                            ->addOptionVote($optionId, $product->getId());
                    }
                }
                $review->aggregate();
            }
            echo PHP_EOL . "Finish!" . PHP_EOL;
        }
    }

    protected function getRatingData()
    {
        return $this->ratingCode;
    }
}