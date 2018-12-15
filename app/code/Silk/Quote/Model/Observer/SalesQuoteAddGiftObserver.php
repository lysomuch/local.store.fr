<?php
/**
 * Copyright (c) 2016, SILK Software
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. All advertising materials mentioning features or use of this software
 *    must display the following acknowledgement:
 *    This product includes software developed by the SILK Software.
 * 4. Neither the name of the SILK Software nor the
 *   names of its contributors may be used to endorse or promote products
 *    derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY SILK Software ''AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL SILK Software BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * Created by PhpStorm.
 * User: Bob song <song01140228@163.com>
 * Date: 17-3-13
 * Time: 13:13
 */

namespace Silk\Quote\Model\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SalesQuoteAddGiftObserver implements  ObserverInterface
{
    /**
     * @var \Silk\Gift\Model\Product
     */
    protected $_gift;

    protected $_cart;

    protected $_store;

    protected $_product;

    protected $_config;

    protected $_resources;

    protected $_giftCollection = array();

    protected $addProduct = false;
    
    /** @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface */
    protected $_date;

    /**
     * SalesQuoteAddGiftObserver constructor.
     * @param \Silk\Gift\Model\Product $giftProduct
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $store
     * @param \Magento\Catalog\Model\Product $product
     */
    public function __construct(
        \Silk\Gift\Model\Product $giftProduct,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $store,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
    )
    {
        $this->_gift = $giftProduct;
        $this->_cart = $cart;
        $this->_store = $store;
        $this->_product = $product;
        $this->_resources = $resourceConnection;
        $this->_config = $scopeConfig;
        $this->_date = $date;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (!$this->_config->getValue('promotion/gift_product/active')) {
            return;
        }
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getQuote();
        $quoteId = $quote->getId();
        // update cart quote
        if ($this->_cart->getQuote()->getId() != $quoteId) {
            $this->_cart->setQuote($quote);
            $this->_cart->save();
        }
//        if (!$quote->getTriggerRecollect() && $this->addProduct) {
//            return;
//        }
        if ($quote->getHasGiftProduct()) {
            if ($this->addProduct) {
                return;
            }
            if ($quote->getItemsCount()) {
                $giftCollection = $this->_getGiftCollection();
                if (count($giftCollection)) {
                    $update = false;
                    $add = false;
                    $subtotalWithDiscount = $quote->getSubtotalWithDiscount();
                    foreach ($giftCollection as $item) {
                        if (!($subtotalWithDiscount >= $item->getMinPrice() && $subtotalWithDiscount <= $item->getMaxPrice())) {
                            // delete product
                            $product = $this->_product->load($item->getProductId());
                            if ($product->getId() && $item->getQty()) {
                                $quoteItem = $quote->getItemByProduct($product);
                                if ($quoteItem && $quoteItem->getId() && $quoteItem->getIsGiftProduct()) {
                                    $update = true;
                                    $quote->removeItem($quoteItem->getId());
                                }
                            }
                        } else {
                            // update product
                            $product = $this->_product->load($item->getProductId());
                            $quoteItem = $quote->getItemByProduct($product);
                            if ($quoteItem && $quoteItem->getId() && ($quoteItem->getQty() != $item->getQty())) {
                                $update = true;
                                $qty = $item->getQty()? $item->getQty(): 0;
                                $idata = array('qty' => $qty);
                                $quote->updateItem($quoteItem->getId(), new \Magento\Framework\DataObject($idata));
                                $quote->setHasGiftProduct(1);
                            }
                            if (!$quoteItem) {
                                // add product
                                $add = $this->addProductToCart($quote, $item, $add);
                            }
                        }
                    }
                    if ($update && !$add) {
                        $quote->setTriggerRecollect(1);
                        $quote->setTotalsCollectedFlag(false);
                        $this->_cart->save();
                        $this->setRecollect($quoteId);
                    }
                    if ($add) {
                        $this->addProduct = true;
                        $quote->setTriggerRecollect(1);
                        $quote->setTotalsCollectedFlag(false);
                        $this->_cart->save();
                        $quote->setTriggerRecollect(0);
                        $this->_cart->save();
                    }
                }
            } else {
                $quote->setHasGiftProduct(0);
                $quote->setTriggerRecollect(1);
                $this->_cart->save();
                $this->setRecollect($quoteId);
            }
        } else {
            if ($quote->getItemsCount()) {
                $giftCollection = $this->_getGiftCollection();
                if (count($giftCollection)) {
                    try {
                        $add = false;
                        $subtotalWithDiscount = $quote->getSubtotalWithDiscount();
                        foreach ($giftCollection as $item) {
                            if ($subtotalWithDiscount >= $item->getMinPrice() && $subtotalWithDiscount <= $item->getMaxPrice()) {
                                // add new product
                                $add = $this->addProductToCart($quote, $item, $add);
                            }
                        }
                        if ($add) {
//                            $this->addProduct = true;
                            $quote->setHasGiftProduct(1);
                            $quote->setTriggerRecollect(1);
                            $this->_cart->save();
                            $quote->setTriggerRecollect(0);
                            $this->_cart->save();
                        }
                    } catch (\Exception $e) {

                    }
                }
            }
        }
    }

    /**
     * @return $this|array
     */
    protected function _getGiftCollection()
    {
        if (!$this->_giftCollection) {
            //get current timezone date, by gan
            $currentDate = $this->_date->date()->format('Y-m-d H:i:s');
            $storeId = $this->_store->getStore()->getId();
            $giftCollection = $this->_gift->getCollection()
                ->addFieldToFilter('is_active', 1)
                ->addFieldToFilter('store_id', $storeId)
                ->addFieldToFilter('start_date', array('lteq' => $currentDate))
                ->addFieldToFilter('end_date', array('gteq' => $currentDate));
            $this->_giftCollection = $giftCollection;
        }

        return $this->_giftCollection;
    }

    /**
     * @param $quoteId
     */
    protected function setRecollect($quoteId)
    {
        $connection= $this->_resources->getConnection();
        $sql = sprintf("UPDATE `quote` SET `trigger_recollect`='0' WHERE `entity_id`='%d';", $quoteId);
        $connection->query($sql);
    }

    /**
     * add gift product to cart
     * @param $quote
     * @param $item
     * @param $isAdd
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function addProductToCart($quote, $item, $isAdd)
    {
        $data = array();
        $data['id'] = $item->getProductId();
        $data['qty'] = $item->getQty()? $item->getQty(): 0;
        if ($data['qty']) {
            $product = $this->_product->load($data['id']);
            if ($product->getId()) {
                $isAdd = true;
                $this->_cart->addProduct($product, $data);
                $quoteItem = $quote->getItemByProduct($product);
                if ($quoteItem && $quoteItem->getProductId()) {
                    $quoteItem->setGiftQty($data['qty']);
                    $quoteItem->setIsGiftProduct(1);
                }
            }
        }
        return $isAdd;
    }
}