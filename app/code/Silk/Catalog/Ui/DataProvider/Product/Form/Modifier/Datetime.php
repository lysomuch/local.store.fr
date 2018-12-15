<?php

/**
 * User: Bob song <song01140228@163.com>
 * @date 18-5-28 下午4:52
 */


namespace Silk\Catalog\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;

class Datetime extends AbstractModifier
{
//    const FIELD_CODE = 'special_from_date';

    public $arrayManager;
    /**
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        ArrayManager $arrayManager
    )
    {
        $this->arrayManager = $arrayManager;
    }
    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $meta = $this->enableTime($meta);
        return $meta;
    }
    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }
    /**
     * @param array $meta
     * @return array
     */
    protected function enableTime(array $meta)
    {
        $attribute = array('special_from_date', 'special_to_date');

        foreach ($attribute as $item) {
            $elementPath = $this->arrayManager->findPath($item, $meta, null, 'children');
            $containerPath = $this->arrayManager->findPath(static::CONTAINER_PREFIX . $item, $meta, null, 'children');
            if (!$elementPath) {
                continue;
            }
            $meta = $this->arrayManager->merge(
                $containerPath,
                $meta,
                [
                    'children' => [
                        $item => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'default' => '',
                                        'options' => [
                                            'dateFormat' > 'Y-m-d',
//                                            'timeFormat' => 'HH:mm:ss',
                                            'timeFormat' => 'HH:mm',
                                            'showsTime' => true
                                        ]
                                    ],
                                ],
                            ],
                        ]
                    ]
                ]
            );
        }


        return $meta;
    }
}