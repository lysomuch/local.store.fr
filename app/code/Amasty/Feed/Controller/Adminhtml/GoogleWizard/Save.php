<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Feed
 */


namespace Amasty\Feed\Controller\Adminhtml\GoogleWizard;

use Amasty\Feed\Model\RegistryContainer;
use Magento\Framework\Exception\LocalizedException;

class Save extends \Amasty\Feed\Controller\Adminhtml\GoogleWizard
{
    /**
     * @var \Amasty\Feed\Model\GoogleWizard
     */
    protected $googleWizard;

    /**
     * @var array
     */
    protected $configSetup = [];

    public function __construct(\Magento\Backend\App\Action\Context $context,
        \Amasty\Feed\Model\RegistryContainer $registryContainer,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Psr\Log\LoggerInterface $logger,
        \Amasty\Feed\Model\GoogleWizard $googleWizard
    ) {
        $this->googleWizard = $googleWizard;
        parent::__construct(
            $context, $registryContainer, $resultLayoutFactory, $logger
        );
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            $data = $this->preparePostData();
            $args = [];

            try {
                $this->configSetup = $this->googleWizard->setup($data);

                $categoryMapperId = RegistryContainer::VAR_CATEGORY_MAPPER;
                $args[$categoryMapperId] = $this->getConfigValue($categoryMapperId);

                $feedId = RegistryContainer::VAR_FEED;
                $args[$feedId] = $this->getConfigValue($feedId);

                $step = RegistryContainer::VAR_STEP;
                $args[$step] = $this->getConfigValue($step);

                if ($this->getRequest()->getParam('setup_complete')) {
                    $this->googleWizard->clearSessionData();
                    $feedId = $args[RegistryContainer::VAR_FEED];
                    $this->_redirect('amfeed/feed/edit', [
                        'id' => $feedId,
                        '_fragment' => 'forcegenerate'
                    ]);
                } else {
                    $this->_redirect('*/*/', $args);
                }

                return;

            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while saving the category mapping. Please review the error log.')
                );
                $this->logger->critical($e);
                $this->_redirect('amfeed/googleWizard/index');

                return;
            }
        }
    }


    /**
     * Get prepared POST
     *
     * @return array
     */
    protected function preparePostData()
    {
        $data = [];
        if ($this->getRequest()->getPostValue()) {
            $postData = $this->getRequest()->getPostValue();

            $postDataKeys = array_keys($postData);
            $data = array_combine($postDataKeys, $postData);
        }

        return $data;
    }

    protected function getConfigValue($key)
    {
        $value = '';
        if (isset($this->configSetup[$key])) {
            $value = $this->configSetup[$key];
        }

        return $value;
    }
}
