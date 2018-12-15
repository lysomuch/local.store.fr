<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Feed
 */


namespace Amasty\Feed\Block\Adminhtml\GoogleWizard;

use Amasty\Feed\Model\RegistryContainer;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Registry
     *
     * @var \Amasty\Feed\Model\RegistryContainer
     */
    protected $registryContainer;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        RegistryContainer $registryContainer,
        array $data = []
    ) {
        $this->registryContainer = $registryContainer;
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
        $this->_objectId = 'feed_id';
        $this->_blockGroup = 'Amasty_Feed';
        $this->_controller = 'adminhtml_googleWizard';

        parent::_construct();

        $this->buttonList->remove('save');
        $this->buttonList->remove('back');
        $this->buttonList->remove('reset');

        $step = $this->registryContainer->getValue(RegistryContainer::VAR_STEP);
        $buttonTitle = __('Proceed to next step');
        if ($step == RegistryContainer::VALUE_LAST_STEP) {
            $buttonTitle = __('Finish and start generation');
        }

        $this->buttonList->add(
            'save_and_continue_edit',
            [
                'label'          => $buttonTitle,
                'class'          => 'save primary',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event'  => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ]
                    ],
                ]
            ],
            10
        );

    }

}