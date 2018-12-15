<?php
/**
 * Created by PhpStorm.
 * User: Nate Gan
 * Email nate.gan@silksoftware.com
 * Date: 2017/11/21
 */

namespace Silk\Gift\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		$installer = $setup;
		$installer->startSetup();
		if (!$installer->tableExists($installer->getTable('gift_product_list'))) {
			$sql = <<<EDO
CREATE TABLE `gift_product_list` (
`gift_id`  int(10) NOT NULL AUTO_INCREMENT COMMENT 'Gift ID' ,
`product_id`  int(10) unsigned NOT NULL COMMENT 'Product ID' ,
`title`  varchar(255) NOT NULL DEFAULT '' COMMENT 'Title' ,
`store_id` smallint(5) unsigned NOT NULL COMMENT 'Store Id',
`qty` int(10) unsigned NULL DEFAULT 0 COMMENT 'Gift Product Qty' ,
`min_price`  decimal(12,4) NOT NULL DEFAULT 0 COMMENT 'Min Price' ,
`max_price`  decimal(12,4) NOT NULL DEFAULT 99999999 COMMENT 'Max Price' ,
`start_date`  datetime DEFAULT NULL COMMENT 'Start Date' ,
`end_date`  datetime DEFAULT NULL COMMENT 'End Date' ,
`is_active`  smallint(6) NOT NULL DEFAULT 1 COMMENT 'Is Gift Active' ,
PRIMARY KEY (`gift_id`),
CONSTRAINT `gift_product_id` FOREIGN KEY (`product_id`) REFERENCES `catalog_product_entity` (`row_id`) ON DELETE CASCADE ,
CONSTRAINT `gift_product_store` FOREIGN KEY (`store_id`) REFERENCES `store` (`store_id`) ON DELETE NO ACTION
) DEFAULT CHARACTER SET=utf8 COMMENT='gift_product_list';
EDO;
			$installer->run($sql);
		}

		$installer->endSetup();
	}
}
