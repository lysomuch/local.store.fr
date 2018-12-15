<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_TimeCountdown
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\TimeCountdown\Controller;

use Magento\Framework\App\RequestInterface;

class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @var \Bss\TimeCountdown\Helper\ModuleConfig
     */
    protected $helperConfig;


    /**
     * Router constructor.
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\App\ResponseInterface $response
     * @param \Bss\TimeCountdown\Helper\ModuleConfig $helperConfig
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\ResponseInterface $response,
        \Bss\TimeCountdown\Helper\ModuleConfig $helperConfig
    ) {
        $this->actionFactory = $actionFactory;
        $this->_response = $response;
        $this->helperConfig=$helperConfig;
    }

    /**
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ActionInterface|void
     */
    function match(RequestInterface $request)
    {
        $frontName = $this->helperConfig->getUrlKey();
        $isUsePage = $this->helperConfig->isEnableModuleTimeCountdown();
        $enableModule = $this->helperConfig->isUsePage();
        $identifier = trim($request->getPathInfo(), '/');
        if($identifier === $frontName && $enableModule && $isUsePage) {
            $request->setModuleName('timecountdown')->setControllerName('category')->setActionName('view');
        } else {
            return;
        }
    }
}
