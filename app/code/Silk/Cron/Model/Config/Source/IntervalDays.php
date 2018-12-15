<?php
namespace Silk\Cron\Model\Config\Source;

class IntervalDays extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Get all options
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                ['label' => __('1'), 'value' => '1'],
                ['label' => __('2'), 'value' => '2'],
                ['label' => __('3'), 'value' => '3'],
                ['label' => __('4'), 'value' => '4'],
                ['label' => __('5'), 'value' => '5'],
                ['label' => __('6'), 'value' => '6'],
                ['label' => __('7'), 'value' => '7'],
                ['label' => __('10'), 'value' => '10'],
                ['label' => __('14'), 'value' => '14'],
                ['label' => __('15'), 'value' => '15'],
                ['label' => __('30'), 'value' => '30'],
            ];
        }
        return $this->_options;
    }
}