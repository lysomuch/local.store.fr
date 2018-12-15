<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Feed
 */


namespace Amasty\Feed\Block\Adminhtml\Feed;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize form
     * Add standard buttons
     * Add "Save and Apply" button
     * Add "Save and Continue" button
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Amasty_Feed';
        $this->_controller = 'adminhtml_feed';

        parent::_construct();

        $model = $this->_coreRegistry->registry('current_amfeed_feed');

        $saveContinueClass = 'save';

        if ($model->getId()) {
            $this->buttonList->add(
                'save_apply',
                [
                    'class' => 'save',
                    'label' => __('Generate'),
                    'data_attribute' => [
                        'mage-init' => [
                            'Amasty_Feed/js/feed/edit' => [
                                'ajaxUrl' => $this->getUrl('*/*/ajax'),
                                'ajax' => true
                            ]
                        ]
                    ]
                ]
            );

        } else {
            $this->buttonList->remove('save');
            $saveContinueClass = 'save primary';
        }

        $this->buttonList->add(
            'save_and_continue_edit',
            [
                'class' => $saveContinueClass,
                'label' => __('Save and Continue Edit'),
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']],
                ]
            ],
            10
        );
    }
}
