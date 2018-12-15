<?php
namespace Silk\Cron\Model\Config\Source;

class IntervalMinutes extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Get all options
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                ['label' => __('10'), 'value' => '10'],
                ['label' => __('20'), 'value' => '20'],
                ['label' => __('30'), 'value' => '30'],
                ['label' => __('40'), 'value' => '40'],
                ['label' => __('50'), 'value' => '50'],
                ['label' => __('60'), 'value' => '60'],
                ['label' => __('90'), 'value' => '90'],
                ['label' => __('120'), 'value' => '120'],
                ['label' => __('150'), 'value' => '150'],
                ['label' => __('180'), 'value' => '180']
            ];
        }
        return $this->_options;
    }
}