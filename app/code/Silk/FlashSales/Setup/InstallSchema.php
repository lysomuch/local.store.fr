<?php
/**
 * All rights reserved.
 *
 * @authors bob.song (song01140228@163.com)
 * @date    18-5-6 下午5:27
 * @version 0.1.0
 */


namespace Silk\FlashSales\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $_objManger = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Eav\Setup\EavSetupFactory $eavSetup */
        $eavSetup = $_objManger->create('\Magento\Eav\Setup\EavSetupFactory');

        $eavSetup = $eavSetup->create();

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'is_flash_sales',
            [
                'group' => 'Advanced Pricing',
                'type' => 'int',
                'frontend' => '',
                'label' => 'Enable Flash Sales',
                'input' => 'select',
                'class' => '',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'default' => '0',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => 'simple,virtual,bundle,downloadable,configurable'
            ]
        );
    }
}