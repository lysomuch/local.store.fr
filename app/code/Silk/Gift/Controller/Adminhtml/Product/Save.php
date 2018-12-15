<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Silk\Gift\Controller\Adminhtml\Product;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
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
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (($data = $this->getRequest()->getPostValue()) && ($giftId = $this->getRequest()->getParam('id'))) {
            $gift = $this->_giftProduct->load($giftId);
            if (!$gift->getId()) {
                $this->messageManager->addError(__('The gift product was removed by another user or does not exist.'));
            } else {
                try {
                    $gift->addData($data)->save();
                    $this->messageManager->addSuccess(__('You saved the gift product.'));
                } catch (LocalizedException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('Something went wrong while saving this gift product.'));
                }
            }
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }
        $resultRedirect->setPath('gift/*/');
        return $resultRedirect;
    }
}
