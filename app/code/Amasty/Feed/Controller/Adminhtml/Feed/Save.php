<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Feed
 */


namespace Amasty\Feed\Controller\Adminhtml\Feed;

use Amasty\Feed\Model\Indexer\Feed\FeedRuleProcessor;
use Amasty\Feed\Model\Rule;
use Magento\Framework\Exception\LocalizedException;

class Save extends \Amasty\Feed\Controller\Adminhtml\Feed
{
    /**
     * @var \Amasty\Base\Model\Serializer
     */
    private $serializer;

    /**
     * @var \Amasty\Feed\Model\Indexer\Feed\FeedRuleProcessor
     */
    private $feedRuleProcessor;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Psr\Log\LoggerInterface $logger,
        \Amasty\Feed\Model\RuleFactory $ruleFactory,
        \Amasty\Base\Model\Serializer $serializer,
        FeedRuleProcessor $feedRuleProcessor
    ) {
        parent::__construct($context, $coreRegistry, $resultLayoutFactory, $logger, $ruleFactory);
        $this->serializer = $serializer;
        $this->feedRuleProcessor = $feedRuleProcessor;
    }

    /**
     * @return \Amasty\Feed\Model\Feed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _save()
    {
        /** @var \Amasty\Feed\Model\Feed $model */
        $model = $this->_objectManager->create('Amasty\Feed\Model\Feed');

        if ($this->getRequest()->getPostValue()) {

            $data = $this->getRequest()->getPostValue();

            $id = $this->getRequest()->getParam('feed_id');

            if ($id) {
                $model->load($id);
                if ($id != $model->getId()) {
                    throw new LocalizedException(__('The wrong feed is specified.'));
                }
            }

            if ($data['feed_type'] === 'xml') {
                if ((!isset($data['xml_header']) || !$data['xml_header'])
                    && (!isset($data['xml_footer']) || !$data['xml_footer'])
                ) {
                    $data['xml_header'] = '<?xml version="1.0"?>'
                        . '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0"> <channel>'
                        . '<created_at>{{DATE}}</created_at>';
                    $data['xml_footer'] = '</channel> </rss>';
                }
            }

            if (isset($data['feed_entity_id'])) {
                $data['entity_id'] = $data['feed_entity_id'];
            }

            if (isset($data['store_ids'])) {
                $data['store_ids'] = implode(",", $data['store_ids']);
            }

            if (isset($data['cron_time'])) {
                $data['cron_time'] = implode(",", $data['cron_time']);
            }

            if (isset($data['csv_field'])) {
                $data['csv_field'] = $this->serializer->serialize($data['csv_field']);
            }

            if (isset($data['rule']) && isset($data['rule']['conditions'])) {
                $data['conditions'] = $data['rule']['conditions'];

                unset($data['rule']);

                /** @var Rule $rule */
                $rule = $this->ruleFactory->create();
                $rule->loadPost($data);

                $data['conditions_serialized'] = $this->serializer->serialize($rule->getConditions()->asArray());
                unset($data['conditions']);
            }

            if (isset($data['entity_id']) && $data['entity_id'] === "") {
                $data['entity_id'] = null;
            }

            $model->setData($data);

            $this->_session->setPageData($model->getData());

            $model->save();

            if (!$this->feedRuleProcessor->getIndexer(FeedRuleProcessor::INDEXER_ID)->isScheduled()) {
                $this->feedRuleProcessor->reindexRow($model->getId());
            }

            $this->_session->setPageData(false);
        }

        return $model;
    }

    public function execute()
    {
        try {
            $data = $this->getRequest()->getPostValue();

            $model = $this->_save();
            $this->messageManager->addSuccessMessage(__('You saved the feed.'));

            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('amfeed/feed/edit', ['id' => $model->getId()]);
                return;
            } else if ($this->getRequest()->getParam('auto_apply')) {
                $this->_redirect('amfeed/feed/export', ['id' => $model->getId()]);
                return;
            }
            $this->_redirect('amfeed/*/');
            return;
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $id = (int)$this->getRequest()->getParam('feed_id');
            if (!empty($id)) {
                $this->_redirect('amfeed/*/edit', ['id' => $id]);
            } else {
                $this->_redirect('amfeed/*/new');
            }
            return;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong while saving the feed data. Please review the error log.')
            );
            $this->logger->critical($e);
            $this->_session->setPageData($data);
            $this->_redirect('amfeed/*/edit', ['id' => $this->getRequest()->getParam('feed_id')]);
            return;
        }

        $this->_redirect('amfeed/*/');
    }
}
