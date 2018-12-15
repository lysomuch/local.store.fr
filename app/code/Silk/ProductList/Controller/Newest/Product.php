<?php
/**
 * Created by PhpStorm.
 * User: cristen
 * Date: 2018/5/18
 * Time: 10:42
 */
namespace Silk\ProductList\Controller\Newest;

use Silk\ProductList\Block\Product\CustomList;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Product extends Action
{
    /** @var PageFactory */
    protected $pageFactory;

    public function __construct(
        Context $context,
        PageFactory $pageFactory
    )
    {
        $this->pageFactory = $pageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->pageFactory->create();

        // get the custom list block and add our collection to it
        /** @var CustomList $list */
        $list = $result->getLayout()->getBlock('custom.product.list');

        //filter conditions array
        $filter = ['is_new_product' => 1];
        $list->setProductCollection($filter);

        return $result;
    }
}