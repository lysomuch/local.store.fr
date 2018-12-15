<?php

/**
 * User: Bob song <song01140228@163.com>
 * @date 18-6-4 下午5:35
 */


namespace Silk\Catalog\Model;


class Config extends \Magento\Catalog\Model\Config
{
    /**
     * @return array
     */
    public function getAttributeUsedForSortByArray()
    {
        $options = ['position' => __('Position'), 'created_at' => __('Sales Date'), 'sales' => __('Sales')];
        foreach ($this->getAttributesUsedForSortBy() as $attribute) {
            /* @var $attribute \Magento\Eav\Model\Entity\Attribute\AbstractAttribute */
            $options[$attribute->getAttributeCode()] = $attribute->getStoreLabel();
        }

        return $options;
    }
}