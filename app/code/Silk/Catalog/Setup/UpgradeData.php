<?php
namespace Silk\Catalog\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Category;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

/**
 * Upgrade the CatalogStaging module DB scheme
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
    )
    {
        $this->eavConfig = $eavConfig;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Upgrades DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $setup->startSetup();


        if (version_compare($context->getVersion(), '0.0.2', '<')) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                'is_new_product',
                [
                    'group' => 'General',
                    'type' => 'varchar',
                    'label' => 'Is New Product',
                    'input' => 'select',
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
//                'frontend' => 'Magento\Catalog\Model\Product\Attribute\Source\Boolean',
//                'backend' => 'Magento\Catalog\Model\Product\Attribute\Source\Boolean',
                    'default' => 0,
                    'required' => false,
                    'sort_order' => 50,
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                    'visible' => true,
                    'is_html_allowed_on_front' => true,
                    'visible_on_front' => true,
                ]
            )->addAttribute(
                Product::ENTITY,
                'is_best_seller',
                [
                    'group' => 'General',
                    'type' => 'varchar',
                    'label' => 'Is Best Seller',
                    'input' => 'select',
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
//                'frontend' => 'Magento\Catalog\Model\Product\Attribute\Source\Boolean',
//                'backend' => 'Magento\Catalog\Model\Product\Attribute\Source\Boolean',
                    'default' => 0,
                    'required' => false,
                    'sort_order' => 50,
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                    'visible' => true,
                    'is_html_allowed_on_front' => true,
                    'visible_on_front' => true
                ]
            )->addAttribute(
                Category::ENTITY,
                'show_in_horizontal_navigation',
                [
                    'type'         => 'varchar',
                    'label'        => 'Show In Horizontal Navigation',
                    'input'        => 'select',
                    'sort_order'   => 100,
                    'source'       => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'global'       => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'default'      => 0,
                    'visible'      => true,
                    'required'     => false,
                    'user_defined' => false,
                    'group'        => '',
                    'backend'      => ''
                ]
            );
        }

        if (version_compare($context->getVersion(), '0.0.3', '<')) {
            $eavSetup->addAttribute(
                Category::ENTITY,
                'show_in_vertical_navigation',
                [
                    'type'         => 'varchar',
                    'label'        => 'Show In Vertical Navigation',
                    'input'        => 'select',
                    'sort_order'   => 100,
                    'source'       => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'global'       => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'default'      => 1,
                    'visible'      => true,
                    'required'     => false,
                    'user_defined' => false,
                    'group'        => '',
                    'backend'      => ''
                ]
            );
        }

        if (version_compare($context->getVersion(), '0.0.4', '<')) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                'is_hot_search',
                [
                    'group' => 'General',
                    'type' => 'varchar',
                    'label' => 'Is Hot Search',
                    'input' => 'select',
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
//                'frontend' => 'Magento\Catalog\Model\Product\Attribute\Source\Boolean',
//                'backend' => 'Magento\Catalog\Model\Product\Attribute\Source\Boolean',
                    'default' => 0,
                    'required' => false,
                    'sort_order' => 50,
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                    'visible' => true,
                    'is_html_allowed_on_front' => true,
                    'visible_on_front' => true
                ]
            )->addAttribute(
                Product::ENTITY,
                'is_limit_quantity',
                [
                    'group' => 'General',
                    'type' => 'varchar',
                    'label' => 'Is Limit Quantity',
                    'input' => 'select',
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
//                'frontend' => 'Magento\Catalog\Model\Product\Attribute\Source\Boolean',
//                'backend' => 'Magento\Catalog\Model\Product\Attribute\Source\Boolean',
                    'default' => 0,
                    'required' => false,
                    'sort_order' => 50,
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                    'visible' => true,
                    'is_html_allowed_on_front' => true,
                    'visible_on_front' => true
                ]
            )->addAttribute(
                Product::ENTITY,
                'custom_tag',
                [
                    'group' => 'General',
                    'type' => 'varchar',
                    'label' => 'Custom Tag',
                    'input' => 'text',
                    'source' => '',
                    'frontend' => '',
                    'backend' => '',
                    'default' => 0,
                    'required' => false,
                    'sort_order' => 50,
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                    'visible' => true,
                    'is_html_allowed_on_front' => true,
                    'visible_on_front' => true
                ]
            );
        }

        if (version_compare($context->getVersion(), '0.0.5', '<')) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                'free_buy',
                [
                    'group' => 'General',
                    'type' => 'varchar',
                    'label' => 'New Customer Free Buy',
                    'input' => 'select',
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
//                'frontend' => 'Magento\Catalog\Model\Product\Attribute\Source\Boolean',
//                'backend' => 'Magento\Catalog\Model\Product\Attribute\Source\Boolean',
                    'default' => 0,
                    'required' => false,
                    'sort_order' => 50,
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                    'visible' => true,
                    'is_html_allowed_on_front' => true,
                    'visible_on_front' => true,
                    'apply_to' => 'simple'
                ]
            );
        }

        if (version_compare($context->getVersion(), '0.0.6', '<')) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                'technical_details',
                [
                    'group' => 'Content',
                    'type' => 'text',
                    'backend' => '',
                    'frontend' => '',
                    'source' => '',
                    'label' => 'Technical Details',
                    'input' => 'textarea',
                    'class' => '',
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => '',
                    'searchable' => false,
                    'filterable' => true,
                    'comparable' => false,
                    'wysiwyg_enabled' => true,
                    'visible_on_front' => true,
                    'is_html_allowed_on_front' => true,
                    'used_in_product_listing' => true,
                    'unique' => false,
                    'apply_to' => ''
                ]
            )->addAttribute(
                Product::ENTITY,
                'product_questions',
                [
                    'group' => 'Content',
                    'type' => 'text',
                    'backend' => '',
                    'frontend' => '',
                    'source' => '',
                    'label' => 'Product Questions',
                    'input' => 'textarea',
                    'class' => '',
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => '',
                    'searchable' => false,
                    'filterable' => true,
                    'comparable' => false,
                    'wysiwyg_enabled' => true,
                    'visible_on_front' => true,
                    'is_html_allowed_on_front' => true,
                    'used_in_product_listing' => true,
                    'unique' => false,
                    'apply_to' => ''
                ]
            );
        }

        if (version_compare($context->getVersion(), '0.0.7', '<')) {
            $eavSetup->removeAttribute(
                Product::ENTITY,
                'technical_details'
            )->removeAttribute(
                Product::ENTITY,
                'product_questions'
            )->addAttribute(
                Product::ENTITY,
                'technical_details',
                [
                    'group' => 'Content',
                    'type' => 'text',
                    'backend' => '',
                    'frontend' => '',
                    'source' => '',
                    'label' => 'Technical Details',
                    'input' => 'textarea',
                    'class' => '',
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => '',
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'wysiwyg_enabled' => true,
                    'visible_on_front' => true,
                    'is_html_allowed_on_front' => true,
                    'used_in_product_listing' => true,
                    'unique' => false,
                    'apply_to' => ''
                ]
            )->addAttribute(
                Product::ENTITY,
                'product_questions',
                [
                    'group' => 'Content',
                    'type' => 'text',
                    'backend' => '',
                    'frontend' => '',
                    'source' => '',
                    'label' => 'Product Questions',
                    'input' => 'textarea',
                    'class' => '',
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => '',
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'wysiwyg_enabled' => true,
                    'visible_on_front' => true,
                    'is_html_allowed_on_front' => true,
                    'used_in_product_listing' => true,
                    'unique' => false,
                    'apply_to' => ''
                ]
            )->addAttribute(
                Product::ENTITY,
                'max_performance',
                [
                    'group' => 'General',
                    'type' => 'varchar',
                    'backend' => '',
                    'frontend' => '',
                    'source' => '',
                    'label' => 'Max Performance(lumens)',
                    'input' => 'text',
                    'class' => '',
                    'required' => false,
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'filterable' => true,
                    'visible' => true,
                    'is_html_allowed_on_front' => true,
                    'visible_on_front' => true
                ]
            )->addAttribute(
                Product::ENTITY,
                'charge_type',
                [
                    'group' => 'General',
                    'type' => 'varchar',
                    'backend' => '',
                    'frontend' => '',
                    'source' => 'Silk\Catalog\Model\Attribute\Source\ChargeType',
                    'label' => 'Charge Type',
                    'input' => 'select',
                    'class' => '',
                    'required' => false,
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'filterable' => true,
                    'visible' => true,
                    'is_html_allowed_on_front' => true,
                    'visible_on_front' => true
                ]
            )->addAttribute(
                Product::ENTITY,
                'compatible_batteries',
                [
                    'group' => 'General',
                    'type' => 'varchar',
                    'backend' => '',
                    'frontend' => '',
                    'source' => 'Silk\Catalog\Model\Attribute\Source\CompatibleBatteries',
                    'label' => 'Compatible Batteries',
                    'input' => 'select',
                    'class' => '',
                    'required' => false,
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'filterable' => true,
                    'visible' => true,
                    'is_html_allowed_on_front' => true,
                    'visible_on_front' => true
                ]
            );
        }

        if (version_compare($context->getVersion(), '0.0.8', '<')) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                'stockout_info',
                [
                    'group' => 'General',
                    'type' => 'varchar',
                    'label' => 'Stockout Information',
                    'input' => 'text',
                    'source' => '',
                    'default' => '',
                    'required' => false,
                    'sort_order' => 50,
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                    'visible' => true,
                    'is_html_allowed_on_front' => true,
                    'visible_on_front' => true,
                    'apply_to' => ''
                ]
            );
        }

        if (version_compare($context->getVersion(), '0.0.9', '<')) {
            $eavSetup->removeAttribute(
                Category::ENTITY,
                'show_in_horizontal_navigation'
            )->removeAttribute(
                Category::ENTITY,
                'show_in_vertical_navigation'
            )->removeAttribute(
                Product::ENTITY,
                'max_performance'
            )->removeAttribute(
                Product::ENTITY,
                'charge_type'
            )->removeAttribute(
                Product::ENTITY,
                'compatible_batteries'
            )->addAttribute(
                Product::ENTITY,
                'max_performance',
                [
                    'group' => 'General',
                    'type' => 'varchar',
                    'backend' => '',
                    'frontend' => '',
                    'source' => '',
                    'label' => 'Max Performance(lumens)',
                    'input' => 'select',
                    'class' => '',
                    'required' => false,
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'filterable' => true,
                    'visible' => true,
                    'is_html_allowed_on_front' => true,
                    'visible_on_front' => true,
                    'user_defined' => true
                ]
            )->addAttribute(
                Product::ENTITY,
                'charge_type',
                [
                    'group' => 'General',
                    'type' => 'varchar',
                    'backend' => '',
                    'frontend' => '',
                    'source' => '',
                    'label' => 'Charge Type',
                    'input' => 'select',
                    'class' => '',
                    'required' => false,
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'filterable' => true,
                    'visible' => true,
                    'is_html_allowed_on_front' => true,
                    'visible_on_front' => true,
                    'user_defined' => true
                ]
            )->addAttribute(
                Product::ENTITY,
                'compatible_batteries',
                [
                    'group' => 'General',
                    'type' => 'varchar',
                    'backend' => '',
                    'frontend' => '',
                    'source' => '',
                    'label' => 'Compatible Batteries',
                    'input' => 'select',
                    'class' => '',
                    'required' => false,
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'filterable' => true,
                    'visible' => true,
                    'is_html_allowed_on_front' => true,
                    'visible_on_front' => true,
                    'user_defined' => true
                ]
            )->addAttribute(
                Product::ENTITY,
                'keywords',
                [
                    'group' => 'Content',
                    'type' => 'varchar',
                    'label' => 'Keywords, more with , separate',
                    'input' => 'text',
                    'source' => '',
                    'default' => '',
                    'required' => false,
                    'sort_order' => 1,
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                    'visible' => true,
                    'is_html_allowed_on_front' => true,
                    'visible_on_front' => true,
                    'apply_to' => ''
                ]
            )->updateAttribute(
                Product::ENTITY, 'custom_tag', 'default_value', ''
            );
        }

        if (version_compare($context->getVersion(), '0.0.10', '<')) {
            $eavSetup->removeAttribute(
                Product::ENTITY,
                'keywords'
            )->addAttribute(
                Product::ENTITY,
                'keywords',
                [
                    'group' => 'Content',
                    'type' => 'varchar',
                    'backend' => '',
                    'frontend' => '',
                    'source' => '',
                    'label' => 'Keywords, more with , separate',
                    'input' => 'textarea',
                    'class' => '',
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'sort_order' => 1,
                    'user_defined' => false,
                    'default' => '',
                    'searchable' => true,
                    'filterable' => false,
                    'comparable' => false,
                    'wysiwyg_enabled' => false,
                    'visible_on_front' => true,
                    'is_html_allowed_on_front' => true,
                    'used_in_product_listing' => true,
                    'unique' => false,
                    'apply_to' => ''
                ]
            );
        }

        if (version_compare($context->getVersion(), '0.0.11', '<')) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                'material_code',
                [
                    'group' => 'General',
                    'type' => 'varchar',
                    'label' => 'Material Code',
                    'input' => 'text',
                    'source' => '',
                    'default' => '',
                    'required' => false,
                    'sort_order' => 30,
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                    'visible' => true,
                    'is_html_allowed_on_front' => true,
                    'visible_on_front' => false,
                    'apply_to' => ''
                ]
            );
        }

        $setup->endSetup();
    }
}