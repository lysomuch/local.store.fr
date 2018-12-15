<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * 批量修改客户所属站点
 */
namespace Silk\Olightcommands\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;

class UpCustomerWebsite extends Command {

    //params
    protected $_limit;
    protected $_mylimit;

    protected $customerCollectionFactory;

    /**
     * @param CollectionFactory $customerCollectionFactory
     * @param Encryptor $encryptor
     */
    public function __construct(CollectionFactory $customerCollectionFactory) {
        parent::__construct();
        $this->customerCollectionFactory = $customerCollectionFactory;
    }

    protected function configure() {
        $this->setName('olightCommands:upCustomerWebsite')
                ->setDescription('Update customer website.')
                ->setDefinition($this->getInputList());
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->collection = $this->customerCollectionFactory->create();
        $this->collection->addAttributeToSelect('*')->addFieldToFilter('website_id','1');
        $this->collection->addAttributeToFilter('created_at', array('lt' => '2018-10-22')); //2018-10-22
        $customerCollection = $this->collection->getItems();

        //var_dump(count($customerCollection));exit;
        foreach ($customerCollection as $key=>$customer) {
            try {
                echo $customer->getId() . PHP_EOL;
                $customer->setWebsiteId(4)->setStoreId(5)->setCreatedIn('usa')->save();
                //$customer->setWebsiteId(2)->setStoreId(2)->setCreatedIn('us')->save();
            } catch (\Exception $e) {
                echo $e->getMessage() . PHP_EOL;
            }
        }
    }

    public function getInputList() {
        $inputList = [];
        $inputList[] = new InputArgument('limit', InputArgument::OPTIONAL, 'Collection Limit as Argument', 100);
        $inputList[] = new InputOption('mylimit', null, InputOption::VALUE_OPTIONAL, 'Collection Limit as Option', 100);
        return $inputList;
    }

}