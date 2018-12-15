<?php
namespace Dotsquares\Imexport\Model\Functional;
class Export extends \Magento\Framework\model\AbstractModel
{
    public function getPaymentMethod($order)
    {
        return $order->getPayment()->getMethod();
    }
    
    public function getChildInfo($item)
    {
        if($item->getParentItemId())
        return 'yes';
        else
        return 'no';
    }
   
    public function getShippingMethod($order)
    {
        if(!$order->getIsVirtual() && $order->getShippingDescription()) {
            return $order->getShippingDescription();
        }
        else if (!$order->getIsVirtual() && $order->getShippingMethod()) {
            return $order->getShippingMethod();
        }
        return '';
    }

    public function getItemSku($item)
    {
        if ($item->getProductType() == "configurable") {
            return $item->getProductOptionByCode('simple_sku');
        }
        return $item->getSku();
    }

    public function formatText($string)
    {
        $string = str_replace(',', ' ', $string);
        return $string;
    }
	
	public function getStoreIds()
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $om->get('Magento\Store\Model\StoreManagerInterface');
        $collection=$storeManager->getStores($withDefault = false);
        $store_ids = array();
        $i = 0;
        foreach($collection as $data)
        {
            $store_ids[$i]['label'] = $data['name'];
            $store_ids[$i]['value'] = $data['store_id'];
            $i++;
        }
        return $store_ids;
    }
	
	public function getCreditMemoDetail($order)
    {
        $credit['adjustment_positive'] = 0;
        $credit['adjustment_negative'] = 0;
        $credit['shipping_amount'] = 0;
        $credit['base_shipping_amount'] = 0;
        $credit['subtotal'] = 0;
        $credit['base_subtotal'] = 0;
        $credit['tax_amount'] = 0;
        $credit['base_tax_amount'] = 0;
        $credit['discount_amount'] = 0;
        $credit['base_discount_amount'] = 0;
        $collection = $order->getCreditmemosCollection();
        if(count($collection))
        {
            foreach($collection as $data)
            {
                $credit['adjustment_positive'] += $data->getData('adjustment_positive');
                $credit['adjustment_negative'] += $data->getData('adjustment_negative');
                $credit['shipping_amount'] += $data->getData('shipping_amount');
                $credit['base_shipping_amount'] += $data->getData('base_shipping_amount');
                $credit['subtotal'] += $data->getData('subtotal');
                $credit['base_subtotal'] += $data->getData('base_subtotal');
                $credit['tax_amount'] += $data->getData('tax_amount');
                $credit['base_tax_amount'] += $data->getData('base_tax_amount');
                $credit['discount_amount'] += $data->getData('discount_amount');
                $credit['base_discount_amount'] += $data->getData('base_discount_amount');
            }
        }
        return $credit;
    }
	
    public function getInvoiceDate($order)
    {
        $date = '';
        $collection = $order->getInvoiceCollection();
        if(count($collection))
        {
            foreach($collection as $data)
            $date = $data->getData('created_at');
        }
        return $date;
    }
    
    public function getShipmentDate($order)
    {
        $date = '';
        $collection = $order->getShipmentsCollection();
        if(count($collection))
        {
            foreach($collection as $data)
            $date = $data->getData('created_at');
        }
        return $date;
    }
    
    public function getCreditmemoDate($order)
    {
        $date = '';
        $collection = $order->getCreditmemosCollection();
        if(count($collection))
        {
            foreach($collection as $data)
            $date = $data->getData('created_at');
        }
        return $date;
    }
}