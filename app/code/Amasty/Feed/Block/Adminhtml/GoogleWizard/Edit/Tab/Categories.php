<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Feed
 */


namespace Amasty\Feed\Block\Adminhtml\GoogleWizard\Edit\Tab;

use Amasty\Feed\Block\Adminhtml\Category\Edit\Tab\Mapping as TabMapping;

class Categories extends TabGeneric
{
    const STEP = 1;
    const HTML_ID_PREFIX = 'feed_googlewizard_categories_';
    const HREF = '<a target="_blank" href="https://support.google.com/merchants/answer/1705911?hl=en">Google Taxonomy</a>';

    /**
     * @var \Amasty\Feed\Model\Category
     */
    private $categoryMapper;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Amasty\Feed\Model\Category $categoryMapper,
        \Amasty\Feed\Model\RegistryContainer $registryContainer,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $registryContainer, $data);
        $this->feldsetId = 'base_fieldset';
        $this->layoutFactory = $layoutFactory;
        $this->categoryMapper = $categoryMapper;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Step 1: Categories');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Step 1: Categories');
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareNotEmptyForm()
    {
        list($categoryMappingId, $feedId, $step) = $this->getFeedStateConfiguration();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix(self::HTML_ID_PREFIX);

        $fieldset = $form->addFieldset($this->feldsetId, ['legend' => $this->getLegend()]);

        if ($categoryMappingId) {
            $this->categoryMapper->loadByCategoryId($categoryMappingId);
            $fieldset->addField(
                'feed_category_id',
                'hidden',
                [
                    'name' => 'feed_category_id',
                    'value' => $categoryMappingId
                ]
            );
        } else {
            $this->categoryMapper->setData('is_active', 1);
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
            'mapping_note',
            'note',
            [
                'name' => 'mapping_note',
                'text' => $this->getMappingNote()
            ]
        );
        
        $fieldset->addField(
            'mapping',
            'note',
            ['name' => 'mapping']
        );

        $className = TabMapping::class;
        $form->getElement(
            'mapping'
        )->setRenderer(
            $this->layoutFactory->create()->createBlock($className)
        );

        $form->setValues($this->categoryMapper->getData());

        $fieldset->addField(
            'category_step',
            'hidden',
            [
                'name' => 'step',
                'value' => $step
            ]
        );

        $this->setForm($form);

        return $this;
    }

    protected function getMappingNote()
    {
        $hrefChange = ['::href' => self::HREF];

        $note = <<<HEREDOC
        Please check ::href and associate your categories to Google's according to requirements.<br/>
        <b>Notice:</b> you should define full path when associating category, just like in taxonomy.<br/>
        For example if you want to associate category where you have Shorts, you should rename it to<br/> 
        "Apparel & Accessories > Clothing > Shorts"
HEREDOC;

        $resultNote = strtr($note, $hrefChange);

        return $resultNote;
    }
}
