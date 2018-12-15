<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Feed
 */


namespace Amasty\Feed\Block\Adminhtml\GoogleWizard\Edit\Tab;

class Delivery extends TabGeneric
{
    const STEP = 4;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Amasty\Feed\Model\Config\Source\Mode
     */
    private $mode;

    public function __construct(\Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Amasty\Feed\Model\Config\Source\Mode $mode,
        \Amasty\Feed\Model\RegistryContainer $registryContainer,
        array $data = []
    ) {
        $this->mode = $mode;
        $this->layoutFactory = $layoutFactory;
        $this->feldsetId = 'amfeed_delivery';
        $this->legend = __('Upload feeds to Google servers automatically?');
        parent::__construct($context, $registry, $formFactory, $registryContainer, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Step 4: Run and Upload');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Step 4: Run and Upload');
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
            'legend' => $this->getLegend()
        ]);

        $fieldset->addField(
            'filename',
            'text',
            [
                'label'    => __('Filename'),
                'name'     => 'filename',
                'class'    => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'delivery_type',
            'select',
            [
                'label' => __('Upload method'),
                'title' => __('Upload method'),
                'name' => 'delivery_type',
                'options' => [
                    \Amasty\Feed\Model\Feed::DELIVERY_TYPE_DLD => __('No, upload manually'),
                    \Amasty\Feed\Model\Feed::DELIVERY_TYPE_FTP => __('Yes, use FTP connection'),
                    \Amasty\Feed\Model\Feed::DELIVERY_TYPE_SFTP => __('Yes, use SFTP connection')
                ],
                'note' => __('You can generate password in Google Merchant Center > Settings > FTP and SFTP')
            ]
        );

        $fieldset->addField(
            'ftp_host',
            'text',
            [
                'name' => 'ftp_host',
                'label' => __('Host'),
                'title' => __('Host'),
                'note' => __('Add port if necessary (example.com:321)')
            ]
        );

        $fieldset->addField(
            'sftp_host',
            'text',
            [
                'name' => 'sftp_host',
                'label' => __('Host'),
                'title' => __('Host'),
                'note' => __('Add port if necessary (example.com:321)')
            ]
        );

        $fieldset->addField(
            'ftp_user',
            'text',
            [
                'name' => 'ftp_user',
                'label' => __('Login'),
                'title' => __('Login')
            ]
        );

        $fieldset->addField(
            'sftp_user',
            'text',
            [
                'name' => 'sftp_user',
                'label' => __('Login'),
                'title' => __('Login')
            ]
        );

        $fieldset->addField(
            'ftp_password',
            'password',
            [
                'name' => 'ftp_password',
                'label' => __('Password'),
                'title' => __('Password')
            ]
        );

        $fieldset->addField(
            'sftp_password',
            'password',
            [
                'name' => 'sftp_password',
                'label' => __('Password'),
                'title' => __('Password')
            ]
        );

        $fieldset->addField(
            'delivery_passive_mode',
            'select',
            [
                'label' => __('Passive Mode'),
                'title' => __('Passive Mode'),
                'name' => 'delivery_passive_mode',
                'options' => ['1' => __('Yes'), '0' => __('No')]
            ]
        );

        $fieldset->addField(
            'execute_mode',
            'select',
            [
                'label'    => __('Generate feed'),
                'name'     => 'execute_mode',
                'values'   => $this->mode->toOptionArray()
            ]
        );

        $fieldset->addField(
            'setup_complete',
            'hidden',
            [
                'name'  => 'setup_complete',
                'value' => 1
            ]
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
            'delivery_step',
            'hidden',
            [
                'name' => 'step',
                'value' => $step
            ]
        );

        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(
                \Magento\Backend\Block\Widget\Form\Element\Dependence::class
            )->addFieldMap(
                'delivery_type',
                'delivery_type_depend'
            )->addFieldMap(
                'ftp_host',
                'ftp_host_depend'
            )->addFieldMap(
                'sftp_host',
                'sftp_host_depend'
            )->addFieldMap(
                'ftp_user',
                'ftp_user_depend'
            )->addFieldMap(
                'sftp_user',
                'sftp_user_depend'
            )->addFieldMap(
                'ftp_password',
                'ftp_password_depend'
            )->addFieldMap(
                'sftp_password',
                'sftp_password_depend'
            )->addFieldMap(
                'delivery_passive_mode',
                'delivery_passive_mode_depend'
            )->addFieldDependence(
                'ftp_host_depend',
                'delivery_type_depend',
                \Amasty\Feed\Model\Feed::DELIVERY_TYPE_FTP
            )->addFieldDependence(
                'sftp_host_depend',
                'delivery_type_depend',
                \Amasty\Feed\Model\Feed::DELIVERY_TYPE_SFTP
            )->addFieldDependence(
                'ftp_user_depend',
                'delivery_type_depend',
                \Amasty\Feed\Model\Feed::DELIVERY_TYPE_FTP
            )->addFieldDependence(
                'sftp_user_depend',
                'delivery_type_depend',
                \Amasty\Feed\Model\Feed::DELIVERY_TYPE_SFTP
            )->addFieldDependence(
                'ftp_password_depend',
                'delivery_type_depend',
                \Amasty\Feed\Model\Feed::DELIVERY_TYPE_FTP
            )->addFieldDependence(
                'sftp_password_depend',
                'delivery_type_depend',
                \Amasty\Feed\Model\Feed::DELIVERY_TYPE_SFTP
            )->addFieldDependence(
                'delivery_passive_mode_depend',
                'delivery_type_depend',
                \Amasty\Feed\Model\Feed::DELIVERY_TYPE_FTP
            )
        );

        $fieldset->addField(
            'step',
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
