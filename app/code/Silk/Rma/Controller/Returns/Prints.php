<?php
/**
 * Created by PhpStorm.
 * User: cristen
 * Date: 2018/7/4
 * Time: 16:52
 */

namespace Silk\Rma\Controller\Returns;


class Prints extends \Magento\Rma\Controller\Returns
{
    /**
     * RMA view page
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->_loadValidRma()) {
            $this->_redirect('*/*/history');
            return;
        }
        /** @var $order \Magento\Sales\Model\Order */
        $order = $this->_objectManager->create(
            \Magento\Sales\Model\Order::class
        )->load(
            $this->_coreRegistry->registry('current_rma')->getOrderId()
        );
        $this->_coreRegistry->register('current_order', $order);

        $this->_view->loadLayout();
        $this->_view->getPage()->getConfig()->getTitle()->set(
            __('Return #%1', $this->_coreRegistry->registry('current_rma')->getIncrementId())
        );

        $this->_view->renderLayout();
    }
}