<?php
/**
 * Created by PhpStorm.
 * User: Nate Gan
 * Email nate.gan@silksoftware.com
 * Date: 2017/11/21
 */

namespace Silk\SalesSequence\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		$installer = $setup;
		$installer->startSetup();

		$table_list = [
            'sequence_creditmemo_0',
		    'sequence_creditmemo_1',
		    'sequence_invoice_0',
		    'sequence_invoice_1',
		    'sequence_order_0',
		    'sequence_order_1',
		    'sequence_rma_item_0',
		    'sequence_rma_item_1',
		    'sequence_shipment_0',
		    'sequence_shipment_1',
        ];

		foreach($table_list as $table) {
            $installer->getConnection()
                ->addColumn(
                    $installer->getTable($table),
                    'date',
                    array(
                        'type'      => Table::TYPE_DATE,
                        'nullable'  => true,
                        'comment'   => 'Date'
                    )
                );

            $installer->getConnection()
                ->addColumn(
                    $installer->getTable($table),
                    'serial_number',
                    array(
                        'type'      => Table::TYPE_INTEGER,
                        'nullable'  => true,
                        'comment'   => 'Serial Number'
                    )
                );

            $installer->getConnection()
                ->addColumn(
                    $installer->getTable($table),
                    'created_at',
                    array(
                        'type'      => Table::TYPE_TIMESTAMP,
                        'nullable'  => true,
                        'default' => Table::TIMESTAMP_INIT,
                        'comment'   => 'Created At'
                    )
                );

            $installer->getConnection()
                ->addIndex(
                    $installer->getTable($table),
                    $installer->getIdxName($table, ['date']),
                    ['date']
                );
        }

		$installer->endSetup();
	}
}
