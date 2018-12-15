<?php

/**
 * Copyright (c) 2016, SILK Software
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. All advertising materials mentioning features or use of this software
 *    must display the following acknowledgement:
 *    This product includes software developed by the SILK Software.
 * 4. Neither the name of the SILK Software nor the
 *   names of its contributors may be used to endorse or promote products
 *    derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY SILK Software ''AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL SILK Software BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * Created by PhpStorm.
 * User: Bob song <song01140228@163.com>
 * Date: 17-3-13
 * Time: 13:13
 */
namespace Silk\Gift\Block\Adminhtml\Product\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Catalog product factory
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

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
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        array $data = []
    ) {
        $this->_productFactory = $productFactory;
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare edit review form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $gift = $this->_coreRegistry->registry('gift_data');
        $product = $this->_productFactory->create()->load($gift->getProductId());

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl(
                        'gift/*/save',
                        [
                            'id' => $this->getRequest()->getParam('id')
                        ]
                    ),
                    'method' => 'post',
                ],
            ]
        );

        $fieldset = $form->addFieldset(
            'gift_details',
            ['legend' => __('Gift Details'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'product_name',
            'note',
            [
                'label' => __('Product'),
                'text' => '<a href="' . $this->getUrl(
                        'catalog/product/edit',
                        ['id' => $product->getId()]
                    ) . '" onclick="this.target=\'blank\'">' . $this->escapeHtml(
                        $product->getName()
                    ) . '</a>'
            ]
        );

        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'title' => __('Title'),
                'label' => __('Title'),
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
        $field = $fieldset->addField(
            'store_id',
            'select',
            [
                'label' => __('Visibility'),
                'required' => true,
                'name' => 'store_id',
                'values' => $this->_systemStore->getStoreValuesForForm()
            ]
        );
        $renderer = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
        );
        $field->setRenderer($renderer);
        $gift->setSelectStores($gift->getStoreId());


        $form->setUseContainer(true);
        $form->setValues($gift->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
