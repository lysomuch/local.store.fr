<?php

namespace Dotsquares\Imexport\Controller\Adminhtml\Import;

use Magento\Framework\App\Filesystem\DirectoryList;

class Importallorders extends \Magento\Backend\App\Action
{
    public function execute()
    {
        if ($this->getRequest()->getPostValue()){
            $files = $this->getRequest()->getFiles();
			if($files['order_csv']['name'] != ''){
                $data = $this->getRequest()->getPostValue();
                $path_info = pathinfo($files['order_csv']['name']);
                $ex=$path_info['extension'];
                if($ex=="CSV" || $ex=="csv"){
                    try{
                        $uploader = $this->_objectManager->create(
                            'Magento\MediaStorage\Model\File\Uploader',
                            ['fileId' => 'order_csv']);
                        $upload=$uploader->setAllowedExtensions(array('csv'));
                        $uploader->setAllowRenameFiles(false);
                        $uploader->setFilesDispersion(false);
                        $dir = $this->_objectManager->get('\Magento\Framework\App\Filesystem\DirectoryList');
                        $mediaDir = $dir->getPath(DirectoryList::MEDIA);
                        $path =$mediaDir.'/dotsquares/ordercsvimport/';
                        $uploader->save($path, $files['order_csv']['name']);
                        $csv = $this->_objectManager->create('Dotsquares\Imexport\Model\Importallorders')->readCSV($path.$files['order_csv']['name'],$data); 
                        $this->_redirect('*/*/orders');
                    }catch (\Exception $e){
                        $this->messageManager->addError('Invalid file type!!');
                        $this->_redirect('*/*/orders');
                    }
                }else{
                    $this->messageManager->addError('Invalid file type!!');
                    $this->_redirect('*/*/orders');
                }
            }else{
                $this->messageManager->addError('Unable to find the import file');
                $this->_redirect('*/*/orders');
            }
        }
    }
}