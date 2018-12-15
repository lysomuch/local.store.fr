<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Silk\Gift\Setup;

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
                ->addColumn($installer->getTable('quote_item'), 'is_gift_product', array(
                    'type'      => Table::TYPE_SMALLINT,
                    'nullable'  => true,
                    'length'    => 4,
                    'comment'   => 'is gift product'
                ));
            $installer->getConnection()
                ->addColumn($installer->getTable('quote_item'), 'gift_qty', array(
                    'type'      => Table::TYPE_INTEGER,
                    'nullable'  => true,
                    'length'    => 11,
                    'comment'   => 'gift product qty'
                ));

            $installer->getConnection()
                ->addColumn($installer->getTable('quote'), 'has_gift_product', array(
                    'type'      => Table::TYPE_SMALLINT,
                    'nullable'  => true,
                    'length'    => 4,
                    'comment'   => 'has gift product'
                ));
            $installer->getConnection()
                ->addColumn($installer->getTable('sales_order_item'), 'is_gift_product', array(
                    'type'      => Table::TYPE_SMALLINT,
                    'nullable'  => true,
                    'length'    => 4,
                    'comment'   => 'is gift product'
                ));
            $installer->getConnection()
                ->addColumn($installer->getTable('sales_order_item'), 'gift_qty', array(
                    'type'      => Table::TYPE_INTEGER,
                    'nullable'  => true,
                    'length'    => 11,
                    'comment'   => 'gift product qty'
                ));

            $installer->getConnection()
                ->addColumn($installer->getTable('sales_order'), 'has_gift_product', array(
                    'type'      => Table::TYPE_SMALLINT,
                    'nullable'  => true,
                    'length'    => 4,
                    'comment'   => 'has gift product'
                ));
        }

        $installer->endSetup();

    }
}
