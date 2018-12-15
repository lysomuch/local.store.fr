<?php
namespace Dotsquares\Imexport\Model;

class selectorder extends \Dotsquares\Imexport\Model\Functional\Export
{
    const ENCLOSURE = '"';
    const DELIMITER = ',';
    public function allorders($orders)
    {
        $i = 0;
        $len = count($orders);
        foreach ($orders as $item) {
            if ($i == 0) {
                $startid = $item;
            } else if ($i == $len - 1) {
                $lastid = $item;
            }
            $i++;
        }
        if($lastid == 0 || $lastid == null || empty($lastid)){
            $lastid = $startid;
        }
        if($startid > $lastid){
            $lastid1 = $lastid;
            $lastid = $startid;
            $startid = $lastid1;
        }
        $fileName = 'exportorder_'.date("Ymd_His").'.csv';
        $object_manager = \Magento\Framework\App\ObjectManager::getInstance();
        $mediaDir = $object_manager->get('Magento\Framework\App\Filesystem\DirectoryList')->getPath('media');
        $locatio = $mediaDir.'/dotsquares/ordercsv'; 
        $fp = fopen($locatio .'/'.$fileName, 'w');
        $this->writeHeadRow($fp);
        foreach ($orders as $order) {
            $objectManagers = \Magento\Framework\App\ObjectManager::getInstance();
            $order = $objectManagers->create('Magento\Sales\Model\Order')->load($order);
            $this->writeOrder($order, $fp);
        }
        fclose($fp);
        return $fileName;
    }
	
    public function selectOrdersdownload($orders)
    {
       $objectManagers = \Magento\Framework\App\ObjectManager::getInstance();
        $i = 0;
        $len = count($orders);
        $lastid = 0;	
        foreach ($orders as $item){
            if($i == 0){
                $startid = $item;
            }else if($i == $len - 1){
                $lastid = $item;
            }
            $i++;
        }
        if($lastid == 0 || $lastid == null || empty($lastid)){
            $lastid = $startid;
        }
        if($startid > $lastid){
            $lastid1 = $lastid;
            $lastid = $startid;
            $startid = $lastid1;
        }
        $fileName = 'exportorder_'.date("Ymd_His").'.csv';
        $object_manager = \Magento\Framework\App\ObjectManager::getInstance();
        $mediaDir = $object_manager->get('Magento\Framework\App\Filesystem\DirectoryList')->getPath('media');
        $locatio = $mediaDir.'/dotsquares/ordercsv'; 
        $fp = fopen($locatio .'/'.$fileName, 'w');
        $this->writeHeadRow($fp);
        foreach ($orders as $order) {
            $order = $objectManagers->create('Magento\Sales\Model\Order')->load($order);
            $this->writeOrder($order, $fp);
        }
        fclose($fp);
        return $fileName;
    }

    public function writeHeadRow($fp)
    {
        $objectManagers = \Magento\Framework\App\ObjectManager::getInstance();
        $head=$objectManagers->create('Dotsquares\Imexport\Model\Table\Csvhead')->getCSVHead();
        fputcsv($fp, $head , self::DELIMITER, self::ENCLOSURE);
    }

    public function writeOrder($order, $fp)
    {
        $objectManagers = \Magento\Framework\App\ObjectManager::getInstance();
        $common =$objectManagers->create('Dotsquares\Imexport\Model\Table\Csvcontent')->getCSVvalue($order);
        $blank = $this->getBlankOrderValues($order);
        $orderItems = $order->getItemsCollection();
        $itemInc = 0;
        $data = array();
        $count = 0;
        foreach ($orderItems as $item)
        {
            if($count==0)
            {
                $record = array_merge($common, $this->getOrderItemValues($item, $order, ++$itemInc));
                fputcsv($fp, $record, self::DELIMITER, self::ENCLOSURE);
            }
            else
            {
                $record = array_merge($blank, $this->getOrderItemValues($item, $order, ++$itemInc));
                fputcsv($fp, $record, self::DELIMITER, self::ENCLOSURE);
            }
            $count++;
        }
    }
	
    public function getBlankOrderValues($order)
    {
        return array(
            '','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',
            '','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',
			'','','','','','','','','','','','','','','','','','','','','','','','','');
    }

    public function getOrderItemValues($item, $order, $itemInc=1)
    {
        return array(
            $this->getItemSku($item),
            $this->formatText($item->getName()),
            (int)$item->getQtyOrdered(),
            (int)$item->getQtyInvoiced(),
            (int)$item->getQtyShipped(),
            (int)$item->getQtyRefunded(),
            (int)$item->getQtyCanceled(),
            $item->getProductType(),
            $item->getOriginalPrice(),
            $item->getBaseOriginalPrice(),
            $item->getRowTotal(),
            $item->getBaseRowTotal(),
            $item->getRowWeight(),
            $item->getPriceInclTax(),
            $item->getBasePriceInclTax(),
            $item->getTaxAmount(),
            $item->getBaseTaxAmount(),
            $item->getTaxPercent(),
            $item->getDiscountAmount(),
            $item->getBaseDiscountAmount(),
            $item->getDiscountPercent(),
            $this->getChildInfo($item),
            serialize($item->getdata('product_options'))
        );
    }
}