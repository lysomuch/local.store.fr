<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Feed
 */


namespace Amasty\Feed\Block\Adminhtml\GoogleWizard\Edit\Tab;

use Amasty\Feed\Block\Adminhtml\GoogleWizard\Edit\Tab\Content\EmptyElement;
use Amasty\Feed\Model\RegistryContainer;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

abstract class TabGeneric extends Generic implements TabInterface
{
    const STEP = 1;

    protected $feldsetId = '';

    protected $legend = '';

    /**
     * @var \Amasty\Feed\Model\RegistryContainer
     */
    protected $registryContainer;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Amasty\Feed\Model\RegistryContainer $registryContainer,
        array $data = []
    ) {
        $this->registryContainer = $registryContainer;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass()
    {
        return '';
    }

    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return false;
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public abstract function getTabLabel();

    /**
     * @return \Magento\Framework\Phrase
     */
    public abstract function getTabTitle();

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $currentStep = $this->registryContainer->getValue(RegistryContainer::VAR_STEP);
        if ($currentStep >= static::STEP) {
            $this->prepareNotEmptyForm();
        } else {
            $this->prepareEmptyForm();
        }

        return parent::_prepareForm();
    }


    /**
     * Prepare empty form before rendering HTML
     *
     * @return $this;
     */
    protected function prepareEmptyForm()
    {
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset(
            $this->feldsetId,
            ['legend' => $this->getLegend()]
        );

        $fieldset->addField(
            'empty',
            'text',
            []
        );

        $className = EmptyElement::class;
        $form->getElement(
            'empty'
        )->setRenderer(
            $this->layoutFactory->create()->createBlock(
                $className
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        return $this;
    }

    /**
     * Get state of current feed configuration
     *
     * @return array;
     */
    protected function getFeedStateConfiguration()
    {
        $categoryMappingId = $this->registryContainer->getValue(RegistryContainer::VAR_CATEGORY_MAPPER);
        $feedId = $this->registryContainer->getValue(RegistryContainer::VAR_FEED);
        $step = $this->registryContainer->getValue(RegistryContainer::VAR_STEP);

        return [$categoryMappingId, $feedId, $step];
    }

    /**
     * Get legend of fieldset
     *
     * @return string;
     */
    protected function getLegend()
    {
        return ($this->legend) ? $this->legend : $this->getTabTitle();
    }

    /**
     * Prepare not-empty form before rendering HTML
     *
     * @return $this;
     */
    protected abstract function prepareNotEmptyForm();
}
