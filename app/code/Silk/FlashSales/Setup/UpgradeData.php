<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Silk\FlashSales\Setup;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cms\Model\BlockFactory;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var BlockFactory
     */
    protected $blockFactory;

    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * UpgradeData constructor.
     * @param BlockFactory $blockFactory
     */
    public function __construct(
        BlockFactory $blockFactory,
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
    ) {
        $this->blockFactory = $blockFactory;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */

    /**
     * Upgrades data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if ($context->getVersion() && version_compare($context->getVersion(), '0.0.2') < 0) {
            $cmsBlock = [
                [
                    'title' => 'Home Page Count Down',
                    'identifier' => 'home_page_countdown',
                    'content' => '
                    <div><a href=""><img src="" alt=""></a></div>
                    {{block class="Silk\\FlashSales\\Block\\CountDown" sku="" name="home_page_countdown" template="Silk_FlashSales::flashsales/countdown.phtml"}}
                    <div><a href=""><img src="" alt=""></a></div>
                    ',
                    'is_active' => 1,
                    'stores' => 0,
                ],
            ];

            /** @var \Magento\Cms\Model\Block $block */
            $block = $this->blockFactory->create();
            foreach ($cmsBlock as $item) {
                $block->setData($item)->save();
            }
        }

        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), '0.0.3', '<')) {
            $eavSetup->updateAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'special_from_date',
                'frontend_input_renderer',
                \Silk\Catalog\Ui\DataProvider\Product\Form\Modifier\Datetime::class
            )->updateAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'special_to_date',
                'frontend_input_renderer',
                \Silk\Catalog\Ui\DataProvider\Product\Form\Modifier\Datetime::class
            );
        }
    }
}
