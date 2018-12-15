<?php
namespace Silk\Catalog\Model\Config\Source;

class ProductsNumber extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
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
                ['label' => __('3'), 'value' => '3'],
                ['label' => __('4'), 'value' => '4'],
                ['label' => __('5'), 'value' => '5'],
                ['label' => __('6'), 'value' => '6'],
            ];
        }
        return $this->_options;
    }
}