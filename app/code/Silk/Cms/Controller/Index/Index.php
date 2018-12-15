<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Silk\Cms\Controller\Index;

class Index extends \Magento\Cms\Controller\Index\Index
{
    /**
     * Renders CMS Home page
     *
     * @param string|null $coreRoute
     * @return \Magento\Framework\Controller\Result\Forward
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($coreRoute = null)
    {
        //用户第一次登陆是否跳转的开关
        $firstVisitRedirect = $this->_objectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface')->getValue(
            'customer/startup/first_visit_redirect',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($firstVisitRedirect) {
            //第一次访问，还没有cookie记录
            $cookieManager = $this->_objectManager->get('\Magento\Framework\Stdlib\CookieManagerInterface');
            if ($cookieManager->getCookie('first_visit_redirect') === null) {
                //用户第一次登陆跳转页面
                $redirectUrl = $this->_objectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface')->getValue(
                    'customer/startup/first_visit_redirect_url',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
                if ($redirectUrl) {
                    //跳转
                    $lifetime = $this->_objectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface')->getValue(
                        'customer/startup/first_visit_cookie_lifetime',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    );
                    $lifetime = empty($lifetime) ? 3600 : $lifetime;
                    $cookieMetadataFactory = $this->_objectManager->get('\Magento\Framework\Stdlib\Cookie\CookieMetadataFactory');
                    $sessionManager = $this->_objectManager->get('\Magento\Framework\Session\SessionManagerInterface');
                    $urlInterface = $this->_objectManager->get('\Magento\Framework\UrlInterface');
                    $responseFactory = $this->_objectManager->get('\Magento\Framework\App\ResponseFactory');
                    $metadata = $cookieMetadataFactory
                        ->createPublicCookieMetadata()
                        ->setDuration($lifetime)
                        ->setPath($sessionManager->getCookiePath())
                        ->setDomain($sessionManager->getCookieDomain());
                    $cookieManager->setPublicCookie(
                        'first_visit_redirect',
                        '1',
                        $metadata
                    );

                    $redirectionUrl = $urlInterface->getUrl($redirectUrl);
                    $responseFactory->create()->setRedirect($redirectionUrl)->sendResponse();
                }
            }
        }

        $pageId = $this->_objectManager->get(
            \Magento\Framework\App\Config\ScopeConfigInterface::class
        )->getValue(
            \Magento\Cms\Helper\Page::XML_PATH_HOME_PAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $resultPage = $this->_objectManager->get(\Magento\Cms\Helper\Page::class)->prepareResultPage($this, $pageId);
        if (!$resultPage) {
            /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('defaultIndex');
            return $resultForward;
        }
        return $resultPage;
    }
}
