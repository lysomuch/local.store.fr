<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Feed
 */


namespace Amasty\Feed\Block\Adminhtml\GoogleWizard\Edit\Tab;

use Amasty\Feed\Block\Adminhtml\GoogleWizard\Edit\Tab\Content\Element as TabElement;

class Optional extends TabGeneric
{
    const STEP = 3;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Amasty\Feed\Model\GoogleWizard
     */
    private $googleWizard;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Amasty\Feed\Model\GoogleWizard $googleWizard,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Amasty\Feed\Model\RegistryContainer $registryContainer,
        array $data = []
    ) {
        $this->feldsetId = 'amfeed_optional';
        $this->googleWizard = $googleWizard;
        $this->layoutFactory = $layoutFactory;
        parent::__construct($context, $registry, $formFactory, $registryContainer, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Step 3: Optional Product Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Step 3: Optional Product Information');
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareNotEmptyForm()
    {
        list($categoryMappingId, $feedId, $step) = $this->getFeedStateConfiguration();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset($this->feldsetId, [
            'legend' => $this->getTabTitle()
        ]);

        $fieldset->addField(
            'optional',
            'text',
            [
                'name' => 'optional',
                'value' => $this->googleWizard->getOptionalAttributes(),
                'label' => __('Content'),
                'title' => __('Content'),
                'note' => __('Please select attributes to output in feed')
            ]
        );

        $className = TabElement::class;
        $form->getElement(
            'optional'
        )->setRenderer(
            $this->layoutFactory->create()->createBlock($className)
        );

        if ($categoryMappingId) {
            $fieldset->addField(
                'feed_category_id',
                'hidden',
                [
                    'name' => 'feed_category_id',
                    'value' => $categoryMappingId
                ]
            );
        }

        if ($feedId) {
            $fieldset->addField(
                'feed_id',
                'hidden',
                [
                    'name'  => 'feed_id',
                    'value' => $feedId,
                ]
            );
        }

        $fieldset->addField(
            'optional_step',
            'hidden',
            [
                'name' => 'step',
                'value' => $step
            ]
        );

        $this->setForm($form);

        return $this;
    }
}
