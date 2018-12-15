<?php
namespace Dotsquares\Imexport\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function __construct(
        DirectoryList $directorylist,
        File $file
    ){
        $this->_directoryList = $directorylist;
        $this->_file = $file;
        $pdfPath = $this->_directoryList->getPath('media').'/dotsquares/log';
        if(!is_dir($pdfPath)){
            $ioAdapter = $this->_file;
            $ioAdapter->mkdir($pdfPath, 0775);
        }
        $file1='error.html';
        $data= $pdfPath.'/'.$file1;
        $this->error_file = $data;
        $handle = fopen($this->error_file, "a+");
    }
   
    public $error_file = '';
    
    public function logException(Exception $exception,$order_id,$type,$line_nuber = '') 
    {
        $message = $exception->getMessage();
        $code = $exception->getCode();
        $file = $exception->getFile();
        $line = $exception->getLine();
        $trace = $exception->getTraceAsString();
        $date = date('M d, Y h:iA');
        if($type=='order'){
            $log_message = "<p><strong>Order Id:</strong> {$order_id}
    	    <p><strong>Error Message:</strong> {$message}</p>
            <p><strong>Line Number:</strong> {$line_nuber}</p>
            <p><strong>Date:</strong> {$date}</p>";
        }else if($type=='invoice'){
    	    $log_message = "<p><strong>Invoice Error : </strong></p>
    	    <p><strong>Order Id:</strong> {$order_id}</p>
            <p><strong>Error Message:</strong> {$message}</p>
            <p><strong>Date:</strong> {$date}</p>";
        }else if($type=='shipment'){
    	    $log_message = "<p><strong>Shipment Error : </strong></p>
    	    <p><strong>Order Id:</strong> {$order_id}</p>
            <p><strong>Error Message:</strong> {$message}</p>";
    	}else if($type=='creditmemo'){
    	    $log_message = "<p><strong>Creditmemo Error : </strong></p>
    	    <p><strong>Order Id:</strong> {$order_id}</p>
            <p><strong>Error Message:</strong> {$message}</p>
            <p><strong>Date:</strong> {$date}</p>";
        }
        if(is_file($this->error_file) === false){
            file_put_contents($this->error_file, '');
        }     
        $content = file_get_contents($this->error_file);
        file_put_contents($this->error_file, $content.$log_message);
    }
	
    public function getOrdersGridUrl()
    {
        return $this->_backendUrl->getUrl('imexport/export/orders', ['_current' => true]);
    }
	
    public function isPrintable()
    {
        if(filesize($this->error_file)>67)
        return true;
    }
	
    public function unlinkFile()
    {
        unlink($this->error_file);
    }
	
    public function footer()
    {
        $content = file_get_contents($this->error_file);
        file_put_contents($this->error_file, $content.'<br /><hr /><br /><br />');
    }
	
    public function logAvailable($order_id,$type,$line_nuber) 
    {
        $message = "Order id already exist";
        $date = date('M d, Y h:iA');
        $log_message = "<p><strong>Order Id:</strong> {$order_id}</p>
        <p><strong>Error Message:</strong> {$message}</p>
        <p><strong>Line Number:</strong> {$line_nuber}</p>
        <p><strong>Date:</strong> {$date}</p>";
        if( is_file($this->error_file) === false ) {
            file_put_contents($this->error_file, '');
        }
        $content = file_get_contents($this->error_file);
        file_put_contents($this->error_file, $content.$log_message);
    }
	
    public function header()
    {
        file_put_contents($this->error_file, '<h3 style="text-align:center;">Error information:</h3><hr /><br />');
    }
}