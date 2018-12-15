<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Silk\Gift\Controller\Adminhtml\Product;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Controller\ResultFactory;

class JsonProductInfo extends \Magento\Backend\App\Action
{

    /**
     * @var Registry|null
     */
    protected $coreRegistry = null;
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Silk\Gift\Model\Product
     */
    protected $_giftProduct;

    /**
     * JsonProductInfo constructor.
     * @param Context $context
     * @param Registry $coreRegistry
     * @param \Silk\Gift\Model\Product $giftProduct
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        \Silk\Gift\Model\Product $giftProduct,
        ProductRepositoryInterface $productRepository
    ) {
        $this->_giftProduct = $giftProduct;
        $this->coreRegistry = $coreRegistry;
        $this->productRepository = $productRepository;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $response = new DataObject();
        $id = $this->getRequest()->getParam('id');
        if (intval($id) > 0) {
            $product = $this->productRepository->getById($id);
            $response->setId($id);
            $response->addData($product->getData());
            $response->setError(0);
        } else {
            $response->setError(1);
            $response->setMessage(__('We can\'t retrieve the product ID.'));
        }
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($response->toArray());
        return $resultJson;
    }
}
