<?php
/**
 * All rights reserved.
 *
 * @authors daniel (luo3555@qq.com)
 * @date    18-5-28 下午5:21
 * @version 0.1.0
 */


namespace Silk\LayeredNavigation\Model\Catalog\Layer\Filter;

class FilterList extends \Magento\Catalog\Model\Layer\FilterList
{
    /**
     * @param \Magento\Catalog\Model\Layer $layer
     * @return \Magento\Catalog\Model\Layer\Filter\AbstractFilter[]
     */
    public function getFilters(\Magento\Catalog\Model\Layer $layer)
    {
        if (!count($this->filters)) {
            foreach ($this->filterableAttributes->getList() as $attribute) {
                $this->filters[] = $this->createAttributeFilter($attribute, $layer);
            }
        }
        return $this->filters;
    }
}