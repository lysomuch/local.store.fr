<?php
/**
 * Copyright © Yogesh Khasturi. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Silk\Geoip\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Locker implements ObserverInterface
{
    /**
     * @var \MageWorx\GeoLock\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_ipToCountryRepository;

    /**
    * @var \Magento\Framework\Stdlib\CookieManagerInterface
    */
    protected $_cookieManager;

    /**
     * @var \Magento\Framework\App\ResponseFactory
     */
    private $_responseFactory;
	
	/**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
	private $_cookieMetadataFactory;
	
	/**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
	private $_sessionManager;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    private $_url;

    public function __construct(
        \Magefan\GeoIp\Model\IpToCountryRepository $ipToCountryRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Silk\Geoip\Helper\Data $helper,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $url,
		\Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager
    ) {
        $this->_ipToCountryRepository = $ipToCountryRepository;
        $this->_scopeConfig = $scopeConfig;
        $this->_helper = $helper;
        $this->_cookieManager = $cookieManager;
        $this->_responseFactory = $responseFactory;
        $this->_url = $url;
        $this->_cookieMetadataFactory = $cookieMetadataFactory;
        $this->_sessionManager = $sessionManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /**
        * 使用cookie，记录是不是指定地区的访客
        */
		//判断后台是否开启了地区限制的配置
        $active = $this->_scopeConfig->getValue('getip/geoip_group/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ( 1 == $active ) {
            try {
                //第一次访问，还没有cookie记录
                if ($this->_cookieManager->getCookie('olight_geoip') === null) {
                    //已开启，再获取后台指定哪些国家可以访问
                    $country = $this->_scopeConfig->getValue('getip/geoip_group/country', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                    if ( !empty($country) ) {
                        $countryArr = explode(',', $country);//国家列表

                        //获取跳转地址
                        $url = $this->_scopeConfig->getValue('getip/geoip_group/redirect', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                        $url = empty($url) ? 'https://www.olightworld.com/' : $url;

                        //获取访客是哪个国家的
                        //$visitorCountyCode = @$this->_ipToCountryRepository->getVisitorCountryCode();
                        $visitorCountyCode = @$this->_ipToCountryRepository->getCountryCode($this->getClientIP());
                        if ( $visitorCountyCode and ('ZZ' != $visitorCountyCode) ) {
                            //如果访客不在指定国家，跳到指定url
                            if ( !in_array($visitorCountyCode, $countryArr) ) {
                                //访客不在指定国家内，在cookie中记录
                                //$this->_cookieManager->setPublicCookie('olight_geoip', 1);
                                $metadata = $this->_cookieMetadataFactory
                                    ->createPublicCookieMetadata()
                                    ->setDuration(86400)
                                    ->setPath($this->_sessionManager->getCookiePath())
                                    ->setDomain($this->_sessionManager->getCookieDomain());
                                $this->_cookieManager->setPublicCookie(
                                    'olight_geoip',
                                    '1',
                                    $metadata
                                );

                                $redirectionUrl = $this->_url->getUrl($url);
                                $this->_responseFactory->create()->setRedirect($redirectionUrl)->sendResponse();
                                exit();
                            } else {
                                //访客在指定国家内，在cookie中记录
                                //$this->_cookieManager->setPublicCookie('olight_geoip', 2);
                                $metadata = $this->_cookieMetadataFactory
                                    ->createPublicCookieMetadata()
                                    ->setDuration(86400)
                                    ->setPath($this->_sessionManager->getCookiePath())
                                    ->setDomain($this->_sessionManager->getCookieDomain());
                                $this->_cookieManager->setPublicCookie(
                                    'olight_geoip',
                                    '2',
                                    $metadata
                                );
                            }
                        }
                    }
                }

                //访客不在指定国家内，在cookie中记录
                
				//由于Varnish缓存，导致这里出问题，跳转不了
				if ($this->_cookieManager->getCookie('olight_geoip') == 1) {
                    //获取跳转地址
                    $url = $this->_scopeConfig->getValue('getip/geoip_group/redirect', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                    $url = empty($url) ? 'https://www.olightworld.com/' : $url;

                    $redirectionUrl = $this->_url->getUrl($url);
                    $this->_responseFactory->create()->setRedirect($redirectionUrl)->sendResponse();
                    exit();
                }
				
            } catch (Exception $e) {
                //...
            }
        }
    }    
	
	/**
     * get client ip
     * @return string
     */
	public function getClientIP() {
		$ip = "";
		/* 
		 * 访问时用localhost访问的，读出来的是“::1”是正常情况。 
		 * ：：1说明开启了ipv6支持,这是ipv6下的本地回环地址的表示。 
		 * 使用ip地址访问或者关闭ipv6支持都可以不显示这个。 
		 * */
        try {
            if (isset($_SERVER)) {
                if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                    $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
                } elseif (isset($_SERVER["HTTP_CLIENT_ip"])) {
                    $ip = $_SERVER["HTTP_CLIENT_ip"];
                } else {
                    $ip = $_SERVER["REMOTE_ADDR"];
                }
            } else {
                if (getenv('HTTP_X_FORWARDED_FOR')) {
                    $ip = getenv('HTTP_X_FORWARDED_FOR');
                } elseif (getenv('HTTP_CLIENT_ip')) {
                    $ip = getenv('HTTP_CLIENT_ip');
                } else {
                    $ip = getenv('REMOTE_ADDR');
                }
            }
            if(trim($ip) == "::1"){
                $ip = "127.0.0.1";
            }
            

            //$ip = '183.17.62.43';//test

            return $this->filterAddress($ip);
        } catch (Exception $e) {
            return ;
        }
	}
	
	/**
     * @param string $remoteAddress
     * @return string|null
     */
    private function filterAddress(string $remoteAddress)
    {
        if (strpos($remoteAddress, ',') !== false) {
            $ipList = explode(',', $remoteAddress);
        } else {
            $ipList = [$remoteAddress];
        }
        $ipList = array_filter(
            $ipList,
            function (string $ip) {
                return filter_var(trim($ip), FILTER_VALIDATE_IP);
            }
        );
		
		foreach ($ipList as $ip) {
			if ( !empty($ip) and ('127.0.0.1' != trim($ip)) ) {
				return $ip;
			}
			
		}
        return null;
    }
}