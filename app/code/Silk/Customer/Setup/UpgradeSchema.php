<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Silk\Customer\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '0.0.2') < 0)
        {
            $installer->getConnection()
                ->addColumn($installer->getTable('quote_item'), 'is_new_customer', array(
                    'type'      => Table::TYPE_SMALLINT,
                    'nullable'  => true,
                    'length'    => 4,
                    'comment'   => 'is new customer quote item'
                ));

            $installer->getConnection()
                ->addColumn($installer->getTable('sales_order_item'), 'is_new_customer', array(
                    'type'      => Table::TYPE_SMALLINT,
                    'nullable'  => true,
                    'length'    => 4,
                    'comment'   => 'is new customer order item'
                ));
        }

        if (version_compare($context->getVersion(), '0.0.4') < 0)
        {
            $sql = <<<EDO
CREATE TABLE `oc_customer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_group_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL DEFAULT '0',
  `firstname` varchar(32) NOT NULL,
  `lastname` varchar(32) NOT NULL,
  `email` varchar(96) NOT NULL,
  `telephone` varchar(32) NOT NULL,
  `fax` varchar(32) NOT NULL,
  `password` varchar(40) NOT NULL,
  `salt` varchar(9) NOT NULL,
  `cart` text,
  `wishlist` text,
  `newsletter` tinyint(1) NOT NULL DEFAULT '0',
  `address_id` int(11) NOT NULL DEFAULT '0',
  `custom_field` text NOT NULL,
  `ip` varchar(40) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `approved` tinyint(1) NOT NULL,
  `safe` tinyint(1) NOT NULL,
  `token` text NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET=utf8;
EDO;

            $installer->run($sql);
        }

        $installer->endSetup();

    }
}
