<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Silk\FlashSales\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cms\Model\BlockFactory;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var BlockFactory
     */
    protected $blockFactory;

    /**
     * @param BlockFactory $modelBlockFactory
     */
    public function __construct(
        BlockFactory $modelBlockFactory
    ) {
        $this->blockFactory = $modelBlockFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $cmsBlock = [
            [
                'title' => 'Flash Sales Page Header',
                'identifier' => 'flash_sales_page_header',
                'content' => '
                Flash Sales Page Header Content
                {{block class="Silk\\FlashSales\\Block\\Header" name="flashsales-header" template="Silk_FlashSales::flashsales/header.phtml"}}
                ',
                'is_active' => 1,
                'stores' => 0,
            ],
            [
                'title' => 'Flash Sales Page Footer',
                'identifier' => 'flash_sales_page_footer',
                'content' => 'Flash Sales Page Footer Content',
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
}
