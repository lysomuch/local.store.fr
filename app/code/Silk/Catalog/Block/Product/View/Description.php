<?php
/**
 * Created by PhpStorm.
 * User: cristen
 * Date: 2018/6/8
 * Time: 16:21
 */

namespace Silk\Catalog\Block\Product\View;


class Description extends \Magento\Catalog\Block\Product\View\Description
{
    /**
     * 获取产品关键词列表
     * @return array
     */
    public function getProductKeywords() {
        $keyword_list = [];

        $keywords =  $this->getProduct()->getData('keywords');
        if($keywords) {
            $keyword_list = explode(',', $keywords);
        }

        return $keyword_list;
    }
}