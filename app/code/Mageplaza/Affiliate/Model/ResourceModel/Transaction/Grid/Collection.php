<?php
/**
 * Copyright � 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageplaza\Affiliate\Model\ResourceModel\Transaction\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;

/**
 * Class Collection
 * @package Mageplaza\Affiliate\Model\ResourceModel\Transaction\Grid
 */
class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
	/**
	 * Initialize dependencies.
	 *
	 * @param EntityFactory $entityFactory
	 * @param Logger $logger
	 * @param FetchStrategy $fetchStrategy
	 * @param EventManager $eventManager
	 * @param string $mainTable
	 * @param string $resourceModel
	 */
	public function __construct(
		EntityFactory $entityFactory,
		Logger $logger,
		FetchStrategy $fetchStrategy,
		EventManager $eventManager,
		$mainTable = 'mageplaza_affiliate_transaction',
		$resourceModel = '\Mageplaza\Affiliate\Model\ResourceModel\Transaction'
	)
	{
		parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
	}

	/**
	 * @return $this
	 */
	protected function _initSelect()
	{
		parent::_initSelect();
		$fields = ['status', 'created_at'];
		foreach ($fields as $field) {
			$this->addFilterToMap($field, 'main_table.'.$field);
		}
		$this->getSelect()->joinLeft(
			['campaign' => $this->getTable('mageplaza_affiliate_campaign')],
			'campaign.campaign_id = main_table.campaign_id',
			['campaign_name' => 'name']
		)->joinLeft(
			['customer' => $this->getTable('customer_entity')],
			'customer.entity_id = main_table.customer_id',
			['email']
		);

		return $this;
	}
}
