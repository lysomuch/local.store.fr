<?php
/**
 * Mageplaza_Affiliate extension
 *                     NOTICE OF LICENSE
 *
 *                     This source file is subject to the Mageplaza License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     https://www.mageplaza.com/LICENSE.txt
 *
 * @category  Mageplaza
 * @package   Mageplaza_Affiliate
 * @copyright Copyright (c) 2016
 * @license   https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\Affiliate\Model\ResourceModel;
use Mageplaza\Affiliate\Helper\Data as Helper;

class Campaign extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	/**
	 * Date time handler
	 *
	 * @var \Magento\Framework\Stdlib\DateTime
	 */
	protected $_dateTime;

	/**
	 *
	 * @var \Mageplaza\Affiliate\Helper\Data
	 */
	protected $helper;

	/**
	 * constructor
	 *
	 * @param \Magento\Framework\Stdlib\DateTime $dateTime
	 * @param \Mageplaza\Affiliate\Helper\Data $helper
	 * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
	 */
	public function __construct(
		\Magento\Framework\Stdlib\DateTime $dateTime,
		Helper $helper,
		\Magento\Framework\Model\ResourceModel\Db\Context $context
	)
	{
		$this->_dateTime = $dateTime;
		$this->helper = $helper;
		parent::__construct($context);
	}


	/**
	 * Initialize resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('mageplaza_affiliate_campaign', 'campaign_id');
	}

	/**
	 * Retrieves Campaign Name from DB by passed id.
	 *
	 * @param string $id
	 * @return string|bool
	 */
	public function getCampaignNameById($id)
	{
		$adapter = $this->getConnection();
		$select  = $adapter->select()
			->from($this->getMainTable(), 'name')
			->where('campaign_id = :campaign_id');
		$binds   = ['campaign_id' => (int)$id];

		return $adapter->fetchOne($select, $binds);
	}

	/**
	 * before save callback
	 *
	 * @param \Magento\Framework\Model\AbstractModel|\Mageplaza\Affiliate\Model\Campaign $object
	 * @return $this
	 */
	protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
	{
		if (is_array($object->getWebsiteIds())) {
			$object->setWebsiteIds(implode(',', $object->getWebsiteIds()));
		}

		if (is_array($object->getAffiliateGroupIds())) {
			$object->setWebsiteIds(implode(',', $object->getAffiliateGroupIds()));
		}
		$object->setUpdatedAt((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));
		if ($object->isObjectNew()) {
			$object->setCreatedAt((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));
		}
		if ($object->getCommission() && is_array($object->getCommission())) {
			$object->setCommission($this->helper->serialize($object->getCommission()));
		} else {
			$object->setCommission($this->helper->serialize(array()));
		}

		foreach (['from_date', 'to_date'] as $field) {
			$value = !$object->getData($field) ? null : $object->getData($field);
			$object->setData($field, $this->_dateTime->formatDate($value));
		}

		return parent::_beforeSave($object);
	}

	protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
	{
		parent::_afterLoad($object);
		$commissions = is_null($object->getCommission()) ? 'a:1:{s:6:"tier_1";a:5:{s:4:"type";s:1:"1";s:5:"value";s:1:"1";s:11:"type_second";s:1:"1";s:12:"value_second";s:1:"1";s:4:"name";s:7:"Example";}}' : $object->getCommission();
		$object->setCommission($this->helper->unserialize($commissions));

		return $this;
	}

}
