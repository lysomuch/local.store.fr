<?php
/**
 * Mageplaza_Affiliate extension
 *                     NOTICE OF LICENSE
 *
 *                     This source file is subject to the Mageplaza License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     https://www.mageplaza.com/LICENSE.txt
 *
 * @category  Mageplaza
 * @package   Mageplaza_Affiliate
 * @copyright Copyright (c) 2016
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Affiliate\Ui\Component\Listing\Column;

class Campaign extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Mageplaza\Affiliate\Model\CampaignFactory
     */
    protected $campaignFactory;

    /**
     * constructor
     *
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Mageplaza\Affiliate\Model\CampaignFactory $campaignFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Mageplaza\Affiliate\Model\CampaignFactory $campaignFactory,
        array $components = [],
        array $data = []
        )
    {
        $this->campaignFactory = $campaignFactory->create();
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }


    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $campaignName = [];
                $campaignIds = explode(',', $item['campaign_id']);
                foreach ($campaignIds as $campaignId) {
                    $campaignName[] = $this->campaignFactory->load($campaignId)->getName();
                }
                $item[$this->getData('name')] = implode(', ', $campaignName);
            }
        }
        return $dataSource;
    }
}
