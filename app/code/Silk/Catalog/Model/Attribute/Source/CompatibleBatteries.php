<?php
/**
 * Created by PhpStorm.
 * User: cristen
 * Date: 2018/6/4
 * Time: 15:05
 */

namespace Silk\Catalog\Model\Attribute\Source;


class CompatibleBatteries extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                ['label' => __(''), 'value' => ''],
                ['label' => __('1 x 18650 or 2 x CR123A'), 'value' => '1 x 18650 or 2 x CR123A'],
                ['label' => __('2 x 18650'), 'value' => '2 x 18650'],
                ['label' => __('2 x CR123A/RCR123A'), 'value' => '2 x CR123A/RCR123A'],
                ['label' => __('3 x 18650'), 'value' => '3 x 18650'],
                ['label' => __('4 x 18650'), 'value' => '4 x 18650'],
                ['label' => __('Customised Li-Ion Battery'), 'value' => 'Customised Li-Ion Battery'],
                ['label' => __('Customized 18650'), 'value' => 'Customized 18650'],
                ['label' => __('customised 18650'), 'value' => 'customised 18650'],
                ['label' => __('customised 26650'), 'value' => 'customised 26650'],
                ['label' => __('customised RCR123A HDC'), 'value' => 'customised RCR123A HDC']
            ];
        }
        return $this->_options;
    }
}