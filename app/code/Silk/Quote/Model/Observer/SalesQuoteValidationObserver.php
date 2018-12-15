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

class SalesQuoteValidationObserver implements  ObserverInterface
{

    protected $_store;

    protected $_product;

    protected $_config;

    protected $_helper;

    /**
     * SalesQuoteValidationObserver constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $store
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Model\Product $product
     * @param \Silk\Customer\Helper\Data $helper
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $store,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Product $product,
        \Silk\Customer\Helper\Data $helper
    )
    {
        $this->_store = $store;
        $this->_config = $scopeConfig;
        $this->_product = $product;
        $this->_helper = $helper;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (!$this->_config->getValue('promotion/new_customer/active')) {
            return;
        }
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getQuote();
        if ($quote->getCustomerId()) {
            $isNewCustomer = $this->_helper->getIsNewCustomer($quote->getCustomerId());
            if ($isNewCustomer) {
                if ($this->_helper->hasNewCustomerItem($quote->getId())) {
                    /** @var \Magento\Quote\Model\Quote\Item $item */
                    foreach($quote->getItemsCollection() as $item) {
                        if ($item->getProduct()->getFreeBuy()) {
                            $item->setIsNewCustomer(1);
                            break;
                        }
                    }
                }
            }
        }
    }
}