<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_TimeCountdown
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\TimeCountdown\Model\ResourceModel;

class Rule extends \Magento\CatalogRule\Model\ResourceModel\Rule {
    /**
     * @param $connection
     * @param $websiteId
     * @param $customerGroupId
     * @param $productId
     * @return mixed
     */
    public function preferenceGetRulesFromProduct ($connection,$websiteId, $customerGroupId, $productId) {
        $select = $connection->select()
            ->from($this->getTable('catalogrule_product'))
            ->where('website_id = ?', $websiteId)
            ->where('customer_group_id = ?', $customerGroupId)
            ->where('product_id = ?', $productId);
        return $connection->query($select);
    }
}
