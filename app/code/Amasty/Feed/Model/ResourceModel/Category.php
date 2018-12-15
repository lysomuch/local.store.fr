<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Feed
 */


namespace Amasty\Feed\Model\ResourceModel;

use Amasty\Feed\Model\Category as ModelCategory;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

class Category extends AbstractDb
{
    /**
     * Initialize table nad PK name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('amasty_feed_category', 'feed_category_id');
    }

    /**
     * @param ModelCategory $category
     * @param               $catId
     *
     * @return $this
     */
    public function loadByCategoryId(ModelCategory $category, $catId)
    {
        ($catId) ? $this->load($category, $catId) : $category->setData([]);

        return $this;
    }
}
