<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Silk\Gift\Controller\Adminhtml\Product;

use Magento\Framework\Controller\ResultFactory;

class NewAction extends \Magento\Backend\App\Action
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('New Gift Product'));
        $resultPage->addContent($resultPage->getLayout()->createBlock(\Silk\Gift\Block\Adminhtml\Product\Add::class));
        $resultPage->addContent($resultPage->getLayout()->createBlock(\Silk\Gift\Block\Adminhtml\Product\Product\Grid::class));
        return $resultPage;
    }
}
