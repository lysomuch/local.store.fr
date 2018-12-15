<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Silk\Gift\Block\Adminhtml\Product\Add;

/**
 * Adminhtml add product review form
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Core system store model
     *
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare add review form
     *
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset('add_review_form', ['legend' => __('New Gift')]);

        $fieldset->addField('product_name', 'note', ['label' => __('Product'), 'text' => 'product_name']);

        $fieldset->addField(
            'is_active',
            'select',
            [
                'label' => __('Active'),
                'required' => true,
                'name' => 'is_active',
                'values' => array(0 => __('Not active'), 1 => __('Active'))
            ]
        );

        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'select_stores',
                'select',
                [
                    'label' => __('Visibility'),
                    'required' => true,
                    'name' => 'store_id',
                    'values' => $this->_systemStore->getStoreValuesForForm()
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                \Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element::class
            );
            $field->setRenderer($renderer);
        }

        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'title' => __('Title'),
                'label' => __('Title'),
                'maxlength' => '255',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'qty',
            'text',
            [
                'name' => 'qty',
                'title' => __('Qty'),
                'label' => __('Qty'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'min_price',
            'text',
            [
                'name' => 'min_price',
                'title' => __('Min Price'),
                'label' => __('Min Price'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'max_price',
            'text',
            [
                'name' => 'max_price',
                'title' => __('Max Price'),
                'label' => __('Max Price'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'start_date',
            'date',
            [
                'name' => 'start_date',
                'type' => 'date',
                'title' => __('Start Date'),
                'label' => __('Start Date'),
                'date_format' => $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT),
                'time_format' => $this->_localeDate->getTimeFormat(\IntlDateFormatter::SHORT),
                'required' => true,
                'class' => 'validate-date'
            ]
        );

        $fieldset->addField(
            'end_date',
            'date',
            [
                'name' => 'end_date',
                'title' => __('End Date'),
                'label' => __('End Date'),
                'date_format' => $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT),
                'time_format' => $this->_localeDate->getTimeFormat(\IntlDateFormatter::SHORT),
                'required' => true,
                'class' => 'validate-date'
            ]
        );

        $fieldset->addField('product_id', 'hidden', ['name' => 'product_id']);

        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setAction($this->getUrl('gift/*/post'));

        $this->setForm($form);
    }
}
