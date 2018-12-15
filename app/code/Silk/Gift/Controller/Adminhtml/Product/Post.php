<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Silk\Gift\Controller\Adminhtml\Product;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;

class Post extends \Magento\Backend\App\Action
{

    protected $_giftProduct;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Silk\Gift\Model\Product $giftProduct
    )
    {
        $this->_giftProduct = $giftProduct;
        parent::__construct($context);
    }
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $productId = $this->getRequest()->getParam('product_id', false);
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if ($productId && ($data = $this->getRequest()->getPostValue())) {
            $gift = $this->_giftProduct->setData($data);
            try {
                $gift->save();
                $this->messageManager->addSuccess(__('You saved the review.'));
                $resultRedirect->setPath('gift/*/');
                return $resultRedirect;
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving this gift product.'));
            }
        } else {
            $this->messageManager->addError(__('Please select a product'));
        }
        $resultRedirect->setPath('gift/*/');
        return $resultRedirect;
    }
}
