<?php
/**
 * Created by PhpStorm.
 * User: cristen
 * Date: 2018/6/4
 * Time: 15:05
 */

namespace Silk\Catalog\Model\Attribute\Source;


class ChargeType extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
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
                ['label' => __('DC Adapter'), 'value' => 'DC Adapter'],
                ['label' => __('Magnetic USB charge base'), 'value' => 'Magnetic USB charge base'],
                ['label' => __('Micro-USB'), 'value' => 'Micro-USB'],
                ['label' => __('Optional charger'), 'value' => 'Optional charger'],
                ['label' => __('TypeC'), 'value' => 'TypeC']
            ];
        }
        return $this->_options;
    }
}