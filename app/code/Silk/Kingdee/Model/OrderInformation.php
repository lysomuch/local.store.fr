<?php

/**
 * User: Bob song <song01140228@163.com>
 * @date 18-6-5 上午9:49
 */

namespace Silk\Kingdee\Model;


use Silk\Kingdee\Api\OrderInformationInterface;

class OrderInformation implements OrderInformationInterface
{

    protected $_order;
    protected $productFactory;

    public function __construct
    (
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Sales\Model\Order $order
    )
    {
        $this->productFactory = $productFactory;
        $this->_order = $order;
    }

        /**
     * get order information
     *
     * @api
     * @return mixed
     */
    public function getOrder()
    {
        $result = array();
        $collection = $this->_order->getCollection();
        if ($collection->getSize()) {
            $data = $this->getOrderData($collection);
            $result = array('code' => 200, 'massage' => 'Success', 'data' => $data);
        }

        return json_encode($result);
    }

    /**
     * get orders information by date
     *
     * @param string $start
     * @param string $end
     * @return mixed
     */
    public function getOrderByDate($start, $end)
    {
        $result = array();
        if (!$start && !$end) {
            $result = array('code' => 301, 'massage' => 'The request failed, params error.');
            return json_encode($result);
        }

        $collection = $this->_order->getCollection();
        $collection->addFieldToFilter('created_at', array('gteq' => $start));
        $collection->addFieldToFilter('created_at', array('lteq' => $end));
        if ($collection->getSize()) {
            $data = $this->getOrderData($collection);
            $result = array('code' => 200, 'massage' => 'Success', 'data' => $data);
        }
        return json_encode($result);
    }

    /**
     * @param $collection
     * @return array
     */
    protected function getOrderData($collection)
    {
        $result = array();
        foreach ($collection as $order) {
            if ($order->getId()) {
                $data = array();
                $data['Id'] = $order->getId();
                $data['Number'] = $order->getIncrementId();
                $data['Status'] = $order->getStatus();
                $data['CreatedAt'] = $order->getCreatedAt();
                $data['StoreId'] = $order->getStoreId();
                $data['CustomerId'] = $order->getCustomerId();
                $data['GrandTotal'] = $order->getGrandTotal();
                $data['DiscountAmount'] = $order->getDiscountAmount();
                $data['CustomerEmail'] = $order->getCustomerEmail();
                $data['OrderCurrencyCode'] = $order->getOrderCurrencyCode();
                foreach ($order->getAllItems() as $item) {
                    $productModel = $this->productFactory->create();
                    $product = $productModel->load($item->getProductId());
                    $itemData = array();
                    $itemData['ProductId'] = $item->getProductId();
                    $itemData['Sku'] = $item->getSku();
                    $itemData['MaterialCode'] = $product->getMaterialCode();
                    $itemData['Name'] = $item->getName();
                    $itemData['Price'] = $item->getPrice();
                    $itemData['Qty'] = $item->getQtyOrdered();
                    $itemData['Total'] = $item->getRowTotal();
                    $itemData['DiscountAmount'] = $item->getDiscountAmount();
                    $itemData['ProductOptions'] = $item->getProductOptions();
                    $data['Items'][] = $itemData;
                }
                $result['order'][] = $data;
            }
        }
        return $result;
    }
}