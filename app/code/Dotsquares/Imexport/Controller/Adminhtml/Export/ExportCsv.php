<?php
namespace Dotsquares\Imexport\Controller\Adminhtml\Export;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;

class ExportCsv extends \Magento\Backend\App\Action
{
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        DirectoryList $directorylist,
        File $file,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ){
        $this->resultPageFactory = $resultPageFactory;
        $this->_directoryList = $directorylist;
        $this->_file = $file;
        parent::__construct($context);
    }
	
    public function execute(){
        $massaction_prepare_key = $this->getRequest()->getParam('massaction_prepare_key');
		$pdfPath = $this->_directoryList->getPath('media').'/dotsquares/ordercsv';
        if(!is_dir($pdfPath)){
            $ioAdapter = $this->_file;
            $ioAdapter->mkdir($pdfPath, 0775);
        }
        if($massaction_prepare_key != 'null' && $massaction_prepare_key != ''){
            $order_ids = $this->getRequest()->getParam('id');
            $file =  $this->_objectManager->create('Dotsquares\Imexport\Model\selectorder')->selectOrdersdownload($order_ids);
        }else{
            $orders =  $this->_objectManager->create('Magento\Sales\Model\Order')->getCollection()->addAttributeToSelect('entity_id');
            $order_arr = array();
            foreach ($orders as $order)  {
                $order_arr[] = $order->getId();
            }
            $file =  $this->_objectManager->create('Dotsquares\Imexport\Model\selectorder')->allorders($order_arr);
        }
        $fileName = 'exportorder_'.date("Ymd_His").'.csv';
        $medi=$this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $medi=$medi.'dotsquares/ordercsv'; 
        $data= $medi.'/'.$file;
        $message=$this->messageManager->addSuccess(__('Successfully CSV Export in "media/dotsquares/ordercsv" directory. <a href="'.$data.'">Download</a>'));
        $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData(false);
        $this->_redirect('*/*/orders');
    }
}