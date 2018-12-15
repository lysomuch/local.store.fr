<?php
namespace Silk\Customer\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Customer\Model\Customer;

/**
 * Upgrade the CatalogStaging module DB scheme
 */
class UpgradeData implements UpgradeDataInterface
{
    /** @var \Magento\Customer\Setup\CustomerSetupFactory  */
    protected $customerSetupFactory;

    public function __construct(\Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory)
    {
        $this->customerSetupFactory = $customerSetupFactory;
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
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        $setup->startSetup();

        if (version_compare($context->getVersion(), '0.0.3', '<')) {
            $customerSetup->updateAttribute(
                Customer::ENTITY, 'salesman_code', 'is_used_in_grid', 1
            )->updateAttribute(
                Customer::ENTITY, 'salesman_code', 'is_visible_in_grid', 1
            )->updateAttribute(
                Customer::ENTITY, 'salesman_code', 'is_filterable_in_grid', 1
            );
        }

        $setup->endSetup();
    }
}