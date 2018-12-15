<?php
/**
 * Created by PhpStorm.
 * User: cristen
 * Date: 2018/6/27
 * Time: 19:08
 */

namespace Silk\Rma\Controller\Returns;

use Magento\Rma\Model\Rma;
class Create extends \Magento\Rma\Controller\Returns\Create
{


    /**
     * Customer create new return
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $orderId = (int)$this->getRequest()->getParam('order_id');
        /** @var $order \Magento\Sales\Model\Order */
        $order = $this->_objectManager->create(\Magento\Sales\Model\Order::class)->load($orderId);
        if (empty($orderId)) {
            $this->_redirect('sales/order/history');
            return;
        }
        $this->_coreRegistry->register('current_order', $order);

        if (!$this->_loadOrderItems($orderId)) {
            return;
        }

        /** @var \Magento\Framework\Stdlib\DateTime\DateTime $coreDate */
        $coreDate = $this->_objectManager->get(\Magento\Framework\Stdlib\DateTime\DateTime::class);
        if (!$this->_canViewOrder($order)) {
            $this->_redirect('sales/order/history');
            return;
        }
        $post = $this->getRequest()->getPostValue();
        if ($post && !empty($post['items'])) {
            /** @var $rmaModel \Magento\Rma\Model\Rma */
            $rmaModel = $this->_objectManager->create(\Magento\Rma\Model\Rma::class);
            try {
                $rmaData = [
                    'status' => \Magento\Rma\Model\Rma\Source\Status::STATE_PENDING,
                    'date_requested' => $coreDate->gmtDate(),
                    'order_id' => $order->getId(),
                    'order_increment_id' => $order->getIncrementId(),
                    'store_id' => $order->getStoreId(),
                    'customer_id' => $order->getCustomerId(),
                    'order_date' => $order->getCreatedAt(),
                    'customer_name' => $order->getCustomerName(),
                    'customer_custom_email' => $post['customer_custom_email'],
                ];
                if (!$rmaModel->setData($rmaData)->saveRma($post)) {
                    $url = $this->_url->getUrl('*/*/create', ['order_id' => $orderId]);
                    $this->getResponse()->setRedirect($this->_redirect->error($url));
                    return;
                }
                /** @var $statusHistory \Magento\Rma\Model\Rma\Status\History */
                $statusHistory = $this->_objectManager->create(\Magento\Rma\Model\Rma\Status\History::class);
                $statusHistory->setRmaEntityId($rmaModel->getEntityId());
                $statusHistory->saveSystemComment();
                if (isset($post['rma_comment']) && !empty($post['rma_comment'])) {
                    $comment = $this->_objectManager->create(\Magento\Rma\Model\Rma\Status\History::class);
                    $comment->setRmaEntityId($rmaModel->getEntityId());
                    $comment->saveComment($post['rma_comment'], true, false);
                }
                $statusHistory->sendNewRmaEmail();
                $this->messageManager->addSuccess(__('You submitted Return #%1.', $rmaModel->getIncrementId()));
                $this->getResponse()->setRedirect($this->_redirect->success($this->_url->getUrl('*/*/history')));
                return;
            } catch (\Exception $e) {
                if ($rmaModel->getEntityId()) {
                    $this->messageManager->addSuccess(__('You submitted Return #%1.', $rmaModel->getIncrementId()));
                    $this->messageManager->addError(__('Send rma email failed.'));
                    $this->getResponse()->setRedirect($this->_redirect->success($this->_url->getUrl('*/*/history')));
                    return;
                }
                $this->messageManager->addError(
                    __('We can\'t create a return right now. Please try again later.')
                );
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
            }
        }
        $this->_view->loadLayout();
        $this->_view->getPage()->getConfig()->getTitle()->set(__('Create New Return'));
        if ($block = $this->_view->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        $this->_view->renderLayout();
    }
}