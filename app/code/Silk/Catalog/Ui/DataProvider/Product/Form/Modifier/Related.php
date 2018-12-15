<?php
/**
 * Created by PhpStorm.
 * User: cristen
 * Date: 2018/7/11
 * Time: 14:38
 */

namespace Silk\Catalog\Ui\DataProvider\Product\Form\Modifier;

use Magento\Ui\Component\Form\Fieldset;


class Related extends \Magento\PricePermissions\Ui\DataProvider\Product\Form\Modifier\Related
{
    /**
     * @var string
     */
    private static $previousGroup = 'search-engine-optimization';

    /**
     * @var int
     */
    private static $sortOrder = 90;

    /**
     * {@inheritdoc}
     * @since 101.0.0
     */
    public function modifyMeta(array $meta)
    {
        $meta = array_replace_recursive(
            $meta,
            [
                static::GROUP_RELATED => [
                    'children' => [
                        $this->scopePrefix . static::DATA_SCOPE_RELATED => $this->getRelatedFieldset(),
                        $this->scopePrefix . static::DATA_SCOPE_UPSELL => $this->getUpSellFieldset(),
                        $this->scopePrefix . static::DATA_SCOPE_CROSSSELL => $this->getCrossSellFieldset(),
                    ],
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Related Products, Accessories, and Cross-Sells'),
                                'collapsible' => true,
                                'componentType' => Fieldset::NAME,
                                'dataScope' => static::DATA_SCOPE,
                                'sortOrder' =>
                                    $this->getNextGroupSortOrder(
                                        $meta,
                                        self::$previousGroup,
                                        self::$sortOrder
                                    ),
                            ],
                        ],

                    ],
                ],
            ]
        );

        return $meta;
    }


    /**
     * Prepares config for the Accessories products fieldset
     *
     * @return array
     * @since 101.0.0
     */
    protected function getUpSellFieldset()
    {
        $content = __(
            'An accessories item is offered to the customer as a pricier or higher-quality' .
            ' alternative to the product the customer is looking at.'
        );

        return [
            'children' => [
                'button_set' => $this->getButtonSet(
                    $content,
                    __('Add Accessories Products'),
                    $this->scopePrefix . static::DATA_SCOPE_UPSELL
                ),
                'modal' => $this->getGenericModal(
                    __('Add Accessories Products'),
                    $this->scopePrefix . static::DATA_SCOPE_UPSELL
                ),
                static::DATA_SCOPE_UPSELL => $this->getGrid($this->scopePrefix . static::DATA_SCOPE_UPSELL),
            ],
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__fieldset-section',
                        'label' => __('Accessories Products'),
                        'collapsible' => false,
                        'componentType' => Fieldset::NAME,
                        'dataScope' => '',
                        'sortOrder' => 20,
                    ],
                ],
            ]
        ];
    }
}