<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Silk\Gift\Controller\Adminhtml\Product;

use Magento\Framework\Controller\ResultFactory;

class Delete extends \Magento\Backend\App\Action
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
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $giftId = $this->getRequest()->getParam('id');
        $giftIds = $this->getRequest()->getParam('gift_ids', array());
        if ($giftId) {
            $giftIds[] = $giftId;
            $resultRedirect->setPath('gift/*/edit', ['id' => $giftId]);
        }
        if (is_array($giftIds) && count($giftIds)) {
            try {
                foreach ($giftIds as $id) {
                    $model = $this->_giftProduct->load($id);
                    $model->delete();
                }
                $this->messageManager->addSuccess(__('The gift product has been deleted.'));

                $resultRedirect->setPath('gift/*/');
                return $resultRedirect;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong deleting this gift product.'));
            }
        }
        if ($giftId) {
            return $resultRedirect->setPath('gift/*/edit', ['id' => $giftId]);
        }
        return $resultRedirect->setPath('gift/*/');
    }
}
