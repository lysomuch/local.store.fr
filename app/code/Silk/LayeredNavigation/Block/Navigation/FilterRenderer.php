<?php

/**
 * User: Bob song <song01140228@163.com>
 * @date 18-5-25 下午5:33
 */


namespace Silk\LayeredNavigation\Block\Navigation;

use Magento\Catalog\Model\Layer\Filter\FilterInterface;

class FilterRenderer extends \Magento\LayeredNavigation\Block\Navigation\FilterRenderer
{
    /**
     * @param FilterInterface $filter
     * @return string
     */
    public function render(FilterInterface $filter)
    {
        $this->assign('filterItems', $filter->getItems());
        $this->assign('allUrl', $this->_getAllUrl($filter));
        $this->assign('selectValue', $this->_getSelectValue($filter));
        $html = $this->_toHtml();
        $this->assign('filterItems', []);
        return $html;
    }

    /**
     * @param $filter
     * @return string
     */
    protected function _getAllUrl($filter)
    {
        $code = $filter->getRequestVar();
        $urlParams = [
            '_current' => true,
            '_use_rewrite' => true,
            '_query' => [$code => null],
            '_escape' => true,
        ];
        return $this->getUrl('*/*/*', $urlParams);
    }

    /**
     * @param $filter
     * @return mixed
     */
    protected function _getSelectValue($filter)
    {
        $code = $filter->getRequestVar();
        $value = $this->getRequest()->getParam($code);

        return $value;
    }
}