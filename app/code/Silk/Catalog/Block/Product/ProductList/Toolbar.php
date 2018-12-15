<?php

/**
 * User: Bob song <song01140228@163.com>
 * @date 18-6-4 下午5:37
 */


namespace Silk\Catalog\Block\Product\ProductList;


class Toolbar extends \Magento\Catalog\Block\Product\ProductList\Toolbar
{
    /**
     * Set collection to pager
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;

        $this->_collection->setCurPage($this->getCurrentPage());

        // we need to set pagination only if passed value integer and more that 0
        $limit = (int)$this->getLimit();
        if ($limit) {
            $this->_collection->setPageSize($limit);
        }
        if ($this->_toolbarModel->getOrder() == 'created_at') {
            $this->_collection->addAttributeToSort($this->_toolbarModel->getOrder(), $this->getCurrentDirection());
        } elseif ($this->_toolbarModel->getOrder() == 'sales') {
            $this->_collection->addAttributeToSort('qty_ordered', $this->getCurrentDirection());
        } else {
            if ($this->getCurrentOrder()) {
                $this->_collection->setOrder($this->getCurrentOrder(), $this->getCurrentDirection());
            }
        }
        return $this;
    }
}