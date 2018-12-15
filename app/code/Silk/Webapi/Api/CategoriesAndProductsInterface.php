<?php
namespace Silk\Webapi\Api;

interface CategoriesAndProductsInterface
{
    /**
     * 获取首页所有分类及其产品列表
     *
     * @api
     * @return \Silk\Webapi\Api\Data\ResultInterface
     */
    public function get_categories_and_products();
}