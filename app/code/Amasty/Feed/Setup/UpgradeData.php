<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Feed
 */


namespace Amasty\Feed\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetaData;

    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * @var Operation\UpgradeTo101
     */
    private $upgradeTo101;

    /**
     * @var Operation\UpgradeTo114
     */
    private $upgradeTo114;

    /**
     * @var Operation\UpgradeTo135
     */
    private $upgradeTo135;

    /**
     * @var Operation\UpgradeTo180
     */
    private $upgradeTo180;

    public function __construct(
        \Magento\Framework\App\ProductMetadataInterface $productMetaData,
        \Magento\Framework\App\State $appState,
        Operation\UpgradeTo101 $upgradeTo101,
        Operation\UpgradeTo114 $upgradeTo114,
        Operation\UpgradeTo135 $upgradeTo135,
        Operation\UpgradeTo180 $upgradeTo180
    ) {
        $this->productMetaData = $productMetaData;
        $this->appState = $appState;
        $this->upgradeTo101 = $upgradeTo101;
        $this->upgradeTo114 = $upgradeTo114;
        $this->upgradeTo135 = $upgradeTo135;
        $this->upgradeTo180 = $upgradeTo180;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->appState->emulateAreaCode(
            \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
            [$this, 'upgradeDataWithEmulationAreaCode'],
            [$setup, $context]
        );
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgradeDataWithEmulationAreaCode(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (!$context->getVersion()) {
            return;
        }

        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $this->upgradeTo101->execute();
        } else if (version_compare($context->getVersion(), '1.1.4') < 0) {
            $this->upgradeTo114->execute();
        }

        if (version_compare($context->getVersion(), '1.3.5', '<')
            && $this->productMetaData->getVersion() >= "2.2.0"
        ) {
            $this->upgradeTo135->execute();
        }

        if (version_compare($context->getVersion(), '1.8.0', '<')) {
            $this->upgradeTo180->execute();
        }

        $setup->endSetup();
    }
}
