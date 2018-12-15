<?php
 
namespace Idevaffiliate\Idevaffiliate\Model;
 
use Magento\Framework\ObjectManager\ObjectManager;
 
class Observer implements \Magento\Framework\Event\ObserverInterface {
    protected $_orderFactory;    
	protected $_scopeConfig;
    
    public function __construct(       
        \Magento\Sales\Model\OrderFactory $orderFactory,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig	
    ) {    
        $this->_orderFactory = $orderFactory;
		$this->_scopeConfig = $scopeConfig;        
    }
 
    /**
     * This is the method that fires when the event runs.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
	 
	public function execute(\Magento\Framework\Event\Observer $observer ) { 
 		$orderIds = $observer->getEvent()->getOrderIds();
        
        if (count($orderIds)) {
 			$orderId = $orderIds[0];
 			$order_details = $this->_orderFactory->create()->load($orderId);
			
			$order_id = $order_details->getIncrementId();
			$idev_subtotal = $order_details->getBaseSubtotal(); 
			$idev_discount = $order_details->getBaseDiscountAmount();
			$idev_saleamt = $idev_subtotal + $idev_discount;
			$coupon_code = $order_details->getCouponCode();
			
			$items = $order_details->getAllVisibleItems();
			$skus = array();
			foreach($items as $i):
				$skus[] = $i->getSku();
			endforeach;
			$products_purchased = implode('|', $skus);
			
			$tracking_url = rtrim($this->_scopeConfig->getValue('idevaffiliate/idevaffiliate/idev_tracking_url'), '/') . '/sale.php';
			$tracking_fields = 'profile=54&ip_address='.$_SERVER['REMOTE_ADDR'].'&idev_saleamt='.$idev_saleamt.'&idev_ordernum='.$order_id.'&products_purchased='.$products_purchased.'&coupon_code='.$coupon_code;
					
			//mail('farazahmedmemon@gmail.com', 'Tracking Pixel', $tracking_url.'?'.$tracking_fields, 'From: jim@idevdirect.com');
					
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $tracking_url);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $tracking_fields);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$return = curl_exec($ch);
			curl_close($ch);
 		}
    }
}