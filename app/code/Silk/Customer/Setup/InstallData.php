<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Silk\Customer\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    protected $customerSetupFactory;

    public function __construct(\Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory)
    {
        $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $attributesInfo = [
            'salesman_code' => [
                'type' => 'varchar',
                'label' => 'Salesman Code',
                'input' => 'text',
                'source' => '',
                'sort_order' => 80,
                'position' => 80,
                'required' => false,
                'adminhtml_only' => 1,
            ],
            'is_new_customer' => [
                'type' => 'int',
                'label' => 'New Customer',
                'input' => 'select',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'sort_order' => 90,
                'default' => 0,
                'position' => 90,
                'required' => false,
                'adminhtml_only' => 1,
            ]
        ];

        foreach ($attributesInfo as $attributeCode => $attributeParams) {
            $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, $attributeCode, $attributeParams);
            $attribute = $customerSetup->getEavConfig()->getAttribute('customer', $attributeCode);
            $attribute->setData(
                'used_in_forms',
                ['adminhtml_customer']
            );
            $attribute->save();
        }

        $setup->endSetup();

    }
}
