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

class ResourceRule extends \Magento\CatalogRule\Model\ResourceModel\Rule {
    /**
     * @param $productId
     * @return array
     * @throws \Zend_Db_Statement_Exception
     */
    public function getFromdateAndTodateCatalogRule ($productId) {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(['rule' => $this->getTable('catalogrule')])
            ->join(
                ['cp' => $this->getTable('catalogrule_product')],
                'cp.rule_id = rule.rule_id',
                ['']
            )
            ->where('cp.product_id = ?', $productId)
            ->group('cp.product_id');
        $result = [];
        $data = $connection->query($select);
        while ($value = $data->fetch()) {
            $result[] = $value;
        }
        return $result;
    }
}