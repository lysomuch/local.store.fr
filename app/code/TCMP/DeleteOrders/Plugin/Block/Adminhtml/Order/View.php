<?php

namespace TCMP\DeleteOrders\Plugin\Block\Adminhtml\Order;

/**
 * Class View
 *
 * @package TCMP\DeleteOrders\Plugin\Block\Adminhtml\Order
 */
class View {

	/**
	 * @var \Magento\Framework\AuthorizationInterface
	 */
	protected $_authorization;

	/**
	 * @var \Magento\Framework\UrlInterface
	 */
	protected $_urlBuilder;

	/**
	 * @var \Magento\Framework\App\RequestInterface
	 */
	protected $_request;

	/**
	 * View constructor.
	 *
	 * @param \Magento\Backend\Block\Template\Context $context
	 * @param array                                   $data
	 */
	public function __construct( \Magento\Backend\Block\Template\Context $context, array $data = [] )
	{
		$this->_authorization = $context->getAuthorization();
		$this->_urlBuilder    = $context->getUrlBuilder();
		$this->_request       = $context->getRequest();
	}

	/**
	 * @param \Magento\Sales\Block\Adminhtml\Order\View $view
	 */
	public function beforeSetLayout( \Magento\Sales\Block\Adminhtml\Order\View $view )
	{
		if ( $this->_isAllowedAction( 'TCMP_DeleteOrders::massDelete' ) ) {
			$message = __( 'Are you sure you want to delete the order?' );
			$view->addButton(
				'order_delete',
				[
					'label'   => __( 'Delete' ),
					'onclick' => "confirmSetLocation('{$message}', '{$this->getUrl('tcmpsales/order/delete')}')",
				]
			);
		}
	}

	/**
	 * Check permission for passed action
	 *
	 * @param string $resourceId
	 *
	 * @return bool
	 */
	protected function _isAllowedAction( $resourceId )
	{
		return $this->_authorization->isAllowed( $resourceId );
	}


	/**
	 * Generate url by route and parameters
	 *
	 * @param   string $route
	 * @param   array  $params
	 *
	 * @return  string
	 */
	public function getUrl( $route = '', $params = [] )
	{
		$params['order_id'] = $this->getOrderId();

		return $this->_urlBuilder->getUrl( $route, $params );
	}

	/**
	 * @return mixed
	 */
	private function getOrderId()
	{
		return $this->_request->getParam( 'order_id' );
	}
}