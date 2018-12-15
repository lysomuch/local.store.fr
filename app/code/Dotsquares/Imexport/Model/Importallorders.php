<?php
namespace Dotsquares\Imexport\Model;

class Importallorders extends \Magento\Framework\model\AbstractModel
{
    public $order_info = array();
    public $order_item_info = array();
    public $order_item_flag = 0;
    public function readCSV($csvFile,$data)
    {
        $import_limit = $data['import_limit'];
        $store_id = $data['store_id'];
        $file_handle = fopen($csvFile, 'r');
        $i=0;
		$check_error_log = 0;
        $order_ids = array();
        $decline = array();
        $available = array();
        $email_error = array();
        $shipping_error = array();
        $shipping_method = 0;
        $success = 0;
        $parent_flag = 0;
        $invalid = 0;
        $line_number = 2;
        $total_order = 0;
        $object_manager = \Magento\Framework\App\ObjectManager::getInstance();
		$this->messageManager = $object_manager->create('Magento\Framework\Message\ManagerInterface');
        $isPrintable = $object_manager->create('Dotsquares\Imexport\Helper\Data')->isPrintable();
        $object_manager->create('Dotsquares\Imexport\Helper\Data')->unlinkFile();
        $object_manager->create('Dotsquares\Imexport\Helper\Data')->header();
        feof($file_handle);
        while (!feof($file_handle)){
            $count[] = fgetcsv($file_handle);
            if($i!=0){
                if($count[$i][0]!='' && $parent_flag==0)
                { 
                    $this->insertOrderData($count[$i]);
                    $parent_flag = 1;
                    $total_order++;
                }
                else if($count[$i][91]!='' && $parent_flag == 1 && $count[$i][0]=='')
                {
                    $this->insertOrderItem($count[$i]);
                }
                else if($parent_flag==1)
                {
                    try{
                      $message = $object_manager->create('Dotsquares\Imexport\Model\Createorder')->createOrder($this->order_info,$this->order_item_info,$store_id);
                      $object_manager->create('Dotsquares\Imexport\Model\Createorder')->removeOrderStatusHistory();
                    }catch(\Exception $e) {
                        $object_manager->create('Dotsquares\Imexport\Helper\Data')>logException($e,$this->order_info['increment_id'],'order',$line_number);
                        $object_manager->create('Dotsquares\Imexport\Helper\Data')->footer();
                        $decline[] = $this->order_info['increment_id'];
                        $this->messageManager->addError(' Please check csv for order id '.$this->order_info['increment_id'].' !!');
                        $message = 0;
                    }
                    if($message== 1){
                        $success++;
                    }
                    if($message== 2){
                        $object_manager->create('Dotsquares\Imexport\Helper\Data')->logAvailable($this->order_info['increment_id'],'order',$line_number);
                        $object_manager->create('Dotsquares\Imexport\Helper\Data')->footer();
						$order_ids[] = $this->order_info['increment_id'];
                    }
                    if($message== 4){
                        $order_id = $this->order_info['increment_id'];
						$object_manager->create('Dotsquares\Imexport\Helper\Data')->logAvailable($this->order_info['increment_id'],'order',$line_number);
                        $object_manager->create('Dotsquares\Imexport\Helper\Data')->footer();
                        $shipping_error[] = $this->order_info['increment_id'];
                    }
                    if($message== 5){
                        $order_id = $this->order_info['increment_id'];
						$object_manager->create('Dotsquares\Imexport\Helper\Data')->logAvailable($this->order_info['increment_id'],'order',$line_number);
                        $object_manager->create('Dotsquares\Imexport\Helper\Data')->footer();
                        $email_error[] = $this->order_info['increment_id'];
                    }
                    $this->order_info = array();
                    $this->order_item_info = array();
                    $this->order_item_flag = 0;
                    $this->insertOrderData($count[$i]); 
                    $parent_flag = 1; 
                    $line_number = $i+1;
                    $total_order++;
                }
            }
            $i++;
            if($import_limit < $total_order)
            break;
        }
        $base_url = $object_manager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
        $error_log_file = $base_url.'pub/media/dotsquares/log/error.html'; 
        $id_already_exits = implode(", ",$order_ids);
        if($id_already_exits != ''){
            $check_error_log = 1;
            $this->messageManager->addError($id_already_exits.' Orders ids already exist!!');
        }
        $email_validation_exits = implode(", ",$email_error);
        if($email_validation_exits != ''){
            $check_error_log = 1;
            $this->messageManager->addError('Please check email format for orders id '.$email_validation_exits.' !!');
        }
		
        $shipping_error_id = implode(", ",$shipping_error);
        if($shipping_error_id != ''){
            $check_error_log = 1;
            $this->messageManager->addError('Please check shipping method for orders id '.$shipping_error_id);
        }
        if($check_error_log == 1){
            $this->messageManager->addError('Click <a target="_blank" href="'.$error_log_file.'">View </a> to error log.');
        }
        if($success){
            $this->messageManager->addSuccess('Total '.$success.' order(s) imported successfully!');
        }
        fclose($file_handle);
        return array($success,$decline);
    }
   
    public function insertOrderData($orders_data)
    {
		$sales_order_arr = array();
        $sales_order_item_arr = array();
        $object_manager = \Magento\Framework\App\ObjectManager::getInstance();
        $sales_order=$object_manager->create('Dotsquares\Imexport\Model\Table\Saletable')->getSalesTable();
        $sales_shipping=$object_manager->create('Dotsquares\Imexport\Model\Table\Billingtable')->getbillingtable();
        $sales_billing=$object_manager->create('Dotsquares\Imexport\Model\Table\Billingtable')->getbillingtable();
        $sales_payment = $this->getSalesPayment();
		$sales_order_item=$object_manager->create('Dotsquares\Imexport\Model\Table\Saleitems')->getSaleitems();
        $i = 0;
        $j = 0;
        $k = 0;
        $l = 0;
        $m = 0;
        for($kkl=0; $kkl < sizeof($orders_data) ; $kkl++)
        {
            $order = $orders_data[$kkl];
            if(count($sales_order)>$i)
            {
                $sales_order_arr[$sales_order[$i]]= $order;
            }
            if(count($sales_billing)>$j && $kkl > 61)
            {
				$sales_billing[$j].$sales_order_arr['billing_address'][$sales_billing[$j]]= $order;
                $j++;
            }
            else if(count($sales_shipping)>$k && $kkl > 75)
            {
				$sales_order_arr['shipping_address'][$sales_shipping[$k]]= $order;
                $k++;
            }
            else if(count($sales_payment)>$l && $kkl > 89)
            {
                $sales_order_arr['payment'][$sales_payment[$l]]= $order;
                $l++;
            }
            else if(count($sales_order_item)>$m && $kkl > 90)
            {
                $sales_order_item_arr[$sales_order_item[$m]]= $order;
                $m++;
            }
            $i++;
        }
        $this->order_info = $sales_order_arr;
        $this->order_item_info[$this->order_item_flag] = $sales_order_item_arr;
        $this->order_item_flag++;
    }
   
    public function insertOrderItem($orders_data)
    {
        $sales_order_item_arr = array();
        $object_manager = \Magento\Framework\App\ObjectManager::getInstance();
        $sales_order_item = $object_manager->create('Dotsquares\Imexport\Model\Table\Saleitems')->getSaleitems();
        $i=0;
        for($j=91;$j<count($orders_data); $j++)
        {
            if(count($sales_order_item)>$i)
            $sales_order_item_arr[$sales_order_item[$i]]= $orders_data[$j];
            $i++;
        }
        $this->order_item_info[$this->order_item_flag] = $sales_order_item_arr;
        $this->order_item_flag++;	
    }
	
    public function getSalesPayment()
    {
        return array('method');
    }
}