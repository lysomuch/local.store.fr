<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Feed
 */


namespace Amasty\Feed\Setup;

use Amasty\Feed\Model\Feed;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * @var Operation\UpgradeTo160
     */
    private $upgradeTo160;

    /**
     * @var Operation\UpgradeTo170
     */
    private $upgradeTo170;

    public function __construct(
        \Magento\Framework\App\State $state,
        Operation\UpgradeTo160 $upgradeTo160,
        Operation\UpgradeTo170 $upgradeTo170
    ) {
        $this->appState = $state;
        $this->upgradeTo160 = $upgradeTo160;
        $this->upgradeTo170 = $upgradeTo170;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->addCompressColumns($setup);
        }

        if (version_compare($context->getVersion(), '1.2.1', '<')) {
            $this->addSkipColumn($setup);
        }

        if (version_compare($context->getVersion(), '1.6.0', '<')) {
            $this->upgradeTo160->execute($setup);
        }

        if (version_compare($context->getVersion(), '1.7.0', '<')) {
            $this->appState->emulateAreaCode(
                \Magento\Framework\App\Area::AREA_FRONTEND,
                [$this->upgradeTo170, 'execute'],
                [$setup]
            );
        }

        $setup->endSetup();
    }

    protected function addCompressColumns(SchemaSetupInterface $setup)
    {
        $table = $setup->getTable('amasty_feed_entity');
        $connection = $setup->getConnection();

        $connection->addColumn(
            $table,
            'compress',
            [
                'type'     => Table::TYPE_TEXT,
                'length'   => 255,
                'nullable' => false,
                'default'  => Feed::COMPRESS_NONE,
                'comment'  => 'Compress'
            ]
        );
    }

    protected function addSkipColumn(SchemaSetupInterface $setup)
    {
        $table = $setup->getTable('amasty_feed_category_mapping');
        $connection = $setup->getConnection();

        $connection->addColumn(
            $table,
            'skip',
            [
                'type'     => Table::TYPE_BOOLEAN,
                'length'   => null,
                'nullable' => false,
                'default'  => false,
                'comment'  => 'Skip this category in feed'
            ]
        );
    }
}
