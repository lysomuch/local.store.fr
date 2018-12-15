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
 * Date: 18-5-13
 * Time: 13:13
 */


namespace Silk\Customer\Helper;


class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    )
    {
        $this->_customer = $customer;
        $this->_resource = $resourceConnection;
        parent::__construct($context);
    }

    /**
     * @param $customerId
     * @return bool
     */
    public function getIsNewCustomer($customerId)
    {
        $customer = $this->_customer->load($customerId);
        if ($customer->getId()) {
            $isNewCustomer = $customer->getIsNewCustomer();
            if ($isNewCustomer && $this->_hasOrderHistory($customerId)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $customerId
     * @return bool
     */
    protected function _hasOrderHistory($customerId)
    {
        $sql = sprintf("SELECT entity_id FROM `sales_order` WHERE `customer_id` = '%d';", $customerId);
        $connection = $this->_resource->getConnection();
        $res = $connection->fetchOne($sql);

        if ($res) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param $quoteId
     * @return bool
     */
    public function hasNewCustomerItem($quoteId)
    {
        $sql = sprintf("SELECT item_id FROM `quote_item` WHERE `quote_id` = '%d' AND `is_new_customer` = '1';", $quoteId);
        $connection = $this->_resource->getConnection();
        $res = $connection->fetchOne($sql);

        if ($res) {
            return false;
        } else {
            return true;
        }
    }
}