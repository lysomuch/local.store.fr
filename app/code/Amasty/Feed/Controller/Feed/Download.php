<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Feed
 */


namespace Amasty\Feed\Controller\Feed;


class Download extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;


    /** @var \Amasty\Feed\Model\ResourceModel\Feed\CollectionFactory */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\Controller\Result\Raw
     */
    private $rawResponse;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Amasty\Feed\Model\ResourceModel\Feed\CollectionFactory $collectionFactory,
        \Magento\Framework\Controller\Result\RawFactory $rawFactory
    ) {
        parent::__construct($context);
        $this->fileFactory = $fileFactory;
        $this->filesystem = $filesystem;
        $this->collectionFactory = $collectionFactory;
        $this->rawResponse = $rawFactory->create();
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $filename = $this->getRequest()->getParam('filename');

        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('filename', $filename)
            ->addFieldToFilter('is_template', ['neq' => 1]);

        foreach ($collection as $model) {

            if ($model->getEntityId()) {
                $output = $model->getOutput();
                $this->rawResponse->setContents($output['content']);
                $this->setFileHeaders($output['filename'], $output['content'], $output['mtime']);

                return $this->rawResponse;
            }

            break;
        }

        return $this->_redirect($this->_redirect->getRefererUrl());
    }

    /**
     * @param string $fileName
     * @param string $content
     * @param int $modifiedTime
     */
    protected function setFileHeaders($fileName, $content, $modifiedTime)
    {
        $this->rawResponse->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', 'application/octet-stream', true)
            ->setHeader('Content-Length', strlen($content), true)
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"', true) // не работает для мультибайтовых кодировок
            ->setHeader('Last-Modified', date('r', $modifiedTime), true);
    }
}
