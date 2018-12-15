<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Silk\Cms\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cms\Model\PageFactory;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var pageFactory
     */
    protected $pageFactory;

    /**
     * InstallData constructor.
     * @param PageFactory $pageFactory
     */
    public function __construct(
        PageFactory $pageFactory
    ) {
        $this->pageFactory = $pageFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $cmsPage = [
            'title' => 'FAQ Page',
            'page_layout' => '1column',
            'identifier' => 'faq_page',
            'content_heading' => 'FAQ',
            'content' => "FAQ content",
            'is_active' => 1,
            'stores' => [0],
            'sort_order' => 0
        ];

        /** @var \Magento\Cms\Model\Block $block */
        $page = $this->pageFactory->create();
        $page->setData($cmsPage)->save();
    }
}
