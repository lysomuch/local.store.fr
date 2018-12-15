<?php
namespace Dotsquares\Imexport\Controller\Adminhtml\Import;

class Orders extends \Magento\Backend\App\Action
{
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ){
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
    
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Dotsquares_Imexport::import');
        $resultPage->getConfig()->getTitle()->prepend(__('Orders Import'));
        $resultPage->addBreadcrumb(__('Orders Import'), __('Orders Import'));
        $resultPage->addContent(
            $resultPage->getLayout()->createBlock('Dotsquares\Imexport\Block\Adminhtml\Orders\Form')
        );
        return $resultPage;
    }
}