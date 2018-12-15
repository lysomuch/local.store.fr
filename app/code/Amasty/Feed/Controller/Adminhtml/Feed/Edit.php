<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Feed
 */


namespace Amasty\Feed\Controller\Adminhtml\Feed;

class Edit extends \Amasty\Feed\Controller\Adminhtml\Feed
{
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Amasty\Feed\Model\Feed');

        /** @var \Amasty\Feed\Model\Rule $rule */
        $rule = $this->ruleFactory->create();

        if ($id) {
            $model->load($id);
            if (!$model->getEntityId()) {
                $this->messageManager->addErrorMessage(__('This feed no longer exists.'));
                $this->_redirect('amfeed/*');
                return;
            }
        }

        $rule->setConditions([]);
        $rule->setConditionsSerialized($model->getConditionsSerialized());

        $rule->getConditions()->setJsFormObject('rule_conditions_fieldset');

        // set entered data if was error when we do save
        $data = $this->_session->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->_coreRegistry->register('current_amfeed_feed', $model);
        $this->_coreRegistry->register('current_amfeed_rule', $rule);

        $this->_view->loadLayout();

        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getEntityId() ? $model->getName() : __('New Feed')
        );

        $this->_view->renderLayout();
    }
}
