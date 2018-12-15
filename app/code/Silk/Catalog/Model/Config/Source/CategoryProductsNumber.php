<?php
namespace Silk\Catalog\Model\Config\Source;

class CategoryProductsNumber extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Get all options
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                ['label' => __('2'), 'value' => '2'],
                ['label' => __('4'), 'value' => '4'],
                ['label' => __('6'), 'value' => '6'],
                ['label' => __('8'), 'value' => '8'],
                ['label' => __('10'), 'value' => '10'],
                ['label' => __('12'), 'value' => '12'],
                ['label' => __('14'), 'value' => '14'],
                ['label' => __('16'), 'value' => '16'],
                ['label' => __('18'), 'value' => '18'],
                ['label' => __('20'), 'value' => '20'],
            ];
        }
        return $this->_options;
    }
}