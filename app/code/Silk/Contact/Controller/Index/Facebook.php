<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Silk\Contact\Controller\Index;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;

class Facebook extends \Magento\Framework\App\Action\Action
{
    /** @var \Magento\Framework\Controller\Result\JsonFactory */
    public $jsonEncoder;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    )
    {
        $this->resultFactory = $context->getResultFactory();
        $this->jsonEncoder = $resultJsonFactory;
        parent::__construct($context);
    }


    /**
     * Show Contact Us page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $jsonData = $this->_getFormHtml();

        $resultJson = $this->jsonEncoder->create();
        return $resultJson->setJsonData($jsonData);
    }

    /**
     * @return mixed
     */
    protected function _getFormHtml()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $block = $resultPage->getLayout()->getBlock('facebook.ajax');
        return $block->toHtml();
    }

}
