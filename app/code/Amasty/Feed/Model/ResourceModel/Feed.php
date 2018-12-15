<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Feed
 */


namespace Amasty\Feed\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

class Feed extends AbstractDb
{
    /**
     * Initialize table nad PK name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('amasty_feed_entity', 'entity_id');
    }

    /**
     *  Load an object by feed id
     *
     * @return $this
     */
    public function loadByFeedId(\Amasty\Feed\Model\Feed $feed, $feedId)
    {
        if ($feedId) {
            $this->load($feed, $feedId);
        }

        return $this;
    }
}
