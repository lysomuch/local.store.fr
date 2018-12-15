<?php

namespace Mageplaza\Affiliate\Helper;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Mageplaza\Affiliate\Model\AccountFactory;
use Magento\Cms\Model\BlockFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use \Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use \Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Affiliate\Model\CampaignFactory;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Customer\Helper\View as CustomerViewHelper;
use \Magento\Framework\View\LayoutInterface;
use \Magento\Framework\Registry;
use Mageplaza\Core\Helper\AbstractData;
use Magento\Framework\App\Area;
use Magento\Backend\Model\Session\Quote;
use Magento\Checkout\Model\Session as CheckoutSession;

class Data extends AbstractData
{
	const XML_PATH_AFFILIATE = 'affiliate/';
	const AFFILIATE_COOKIE_NAME = 'affiliate_key';
	const AFFILIATE_COOKIE_SOURCE_NAME = 'affiliate_source';
	const AFFILIATE_COOKIE_SOURCE_VALUE = 'affiliate_source_value';
	const XML_PATH_EMAIL_SENDER = 'affiliate/email/sender';

	protected $accountFactory;
	protected $campaignFactory;
	protected $customerFactory;
	protected $_customerSession;
	protected $priceCurrency;

	/**
	 * CookieManager
	 *
	 * @var CookieManagerInterface
	 */
	protected $cookieManager;
	/**
	 * @var CookieMetadataFactory
	 */
	protected $cookieMetadataFactory;
	/**
	 * Block factory
	 *
	 * @var BlockFactory
	 */
	protected $_blockFactory;

	static protected $_key = [];

	protected $_store;

	/**
	 * @var TransportBuilder
	 */
	private $transportBuilder;

	/**
	 * @var CustomerViewHelper
	 */
	protected $customerViewHelper;

	/**
	 * @var $_layout
	 */
	protected $_layout;

    /**
    * @var \Magento\Framework\Registry\ $registry
    */
    protected $registry;

	/**
	 * @var Checkout Session
	 */
	protected $_checkoutSession;

	static private $_affCache = array();

	public function __construct(
		Context $context,
		ObjectManagerInterface $objectManager,
		AccountFactory $accountFactory,
		CampaignFactory $campaignFactory,
		BlockFactory $blockFactory,
		CustomerFactory $customerFactory,
		CookieManagerInterface $cookieManagerInterface,
		CustomerSession $customerSession,
		CookieMetadataFactory $cookieMetadataFactory,
		PriceCurrencyInterface $priceCurrency,
		StoreManagerInterface $storeManager,
		TransportBuilder $transportBuilder,
		CustomerViewHelper $customerViewHelper,
		LayoutInterface $layout,
		Registry $registry
	)
	{
		$this->_blockFactory         = $blockFactory;
		$this->accountFactory        = $accountFactory;
		$this->customerFactory       = $customerFactory;
		$this->campaignFactory       = $campaignFactory;
		$this->_customerSession      = $customerSession;
		$this->cookieManager         = $cookieManagerInterface;
		$this->cookieMetadataFactory = $cookieMetadataFactory;
		$this->priceCurrency         = $priceCurrency;
		$this->transportBuilder      = $transportBuilder;
		$this->customerViewHelper    = $customerViewHelper;
		$this->_layout				 = $layout;
		$this->registry				 = $registry;

		parent::__construct($context, $objectManager, $storeManager);
	}

	public static function hasCache($cacheKey)
	{
		if (isset(self::$_affCache[$cacheKey])) {
			return true;
		}

		return false;
	}

	public static function saveCache($cacheKey, $value = null)
	{
		self::$_affCache[$cacheKey] = $value;

		return;
	}

	public static function getCache($cacheKey)
	{
		if (isset(self::$_affCache[$cacheKey])) {
			return self::$_affCache[$cacheKey];
		}

		return null;
	}

	public function isEnable($store = null)
	{
		return $this->getAffiliateConfig('general/enable', $store);
	}

	public function getAffiliateConfig($code, $storeId = null)
	{
		return $this->getConfigValue(self::XML_PATH_AFFILIATE . $code, $storeId);
	}

	public function getAffiliateAccount($value, $code = null)
	{
		if ($code) {
			$account = $this->accountFactory->create()->load($value, $code);
		} else {
			$account = $this->accountFactory->create()->load($value);
		}

		return $account;
	}

	public function getCurrentAffiliate()
	{
		$customerId = $this->_customerSession->getCustomerId();

		return $this->getAffiliateAccount($customerId, 'customer_id');
	}

	/**
	 * Get affiliate key
	 * if customer has referred by an other affiliate (has order already), get key from that order
	 * else get key from cookie
	 *
	 * @return null|string
	 */
	public function getAffiliateKey()
	{
		$key = $this->getAffiliateKeyFromCookie();
		if ($this->hasFirstOrder()) {
			$key = $this->getFirstAffiliateOrder()->getAffiliateKey();
		}

		return $key;
	}

	/**
	 * Check customer has referred or not
	 *
	 * @return bool
	 */
	public function hasFirstOrder()
	{
		$firstOrder = $this->getFirstAffiliateOrder();
		if ($firstOrder && $firstOrder->getId()) {
			return true;
		}

		return false;
	}

	/**
	 * Get first order which has been referred by an affiliate
	 *
	 * @return mixed
	 */
	public function getFirstAffiliateOrder()
	{
		$cacheKey = 'affiliate_first_order';
		if (!$this->hasCache($cacheKey)) {
			$customer = $this->getCustomer();
			if ($customer && $customer->getId()) {
				$order = $this->objectManager->create('Magento\Sales\Model\Order')
					->getCollection()
					->addFieldToFilter('customer_id', $customer->getId())
					->addFieldToFilter('affiliate_key', ['notnull' => true]);

				$this->saveCache($cacheKey, $order->getFirstItem());
			}
		}

		return $this->getCache($cacheKey);
	}

	/**
	 * Email will be sent or not
	 *
	 * @param $account
	 * @param $xmlEnablePath
	 * @return bool
	 */
	public function allowSendEmail($account, $xmlEnablePath)
	{
		if (!$this->getAffiliateConfig($xmlEnablePath)) {
			return false;
		}

		if (!$account->getEmailNotification()) {
			return false;
		}

		return true;
	}

	/**
	 * Send corresponding email template
	 *
	 * @param $customer
	 * @param string $template configuration path of email template
	 * @param string $sender configuration path of email identity
	 * @param array $templateParams
	 * @param int|null $storeId
	 * @param string $email
	 * @return $this
	 */
	public function sendEmailTemplate(
		$customer,
		$template,
		$templateParams = [],
		$sender = self::XML_PATH_EMAIL_SENDER,
		$storeId = null,
		$email = null
	)
	{
		$templateId = $this->scopeConfig->getValue($template, ScopeInterface::SCOPE_STORE, $storeId);
		if ($email === null) {
			$email = $customer->getEmail();
		}

		$templateParams['recipient'] = $customer;

		$transport = $this->transportBuilder->setTemplateIdentifier($templateId)
			->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->getWebsiteStoreId($customer, $storeId)])
			->setTemplateVars($templateParams)
			->setFrom($this->scopeConfig->getValue($sender, ScopeInterface::SCOPE_STORE, $storeId))
			->addTo($email, $customer->getName())
			->getTransport();

		$transport->sendMessage();

		return $this;
	}

	/**
	 * Get either first store ID from a set website or the provided as default
	 *
	 * @param CustomerInterface $customer
	 * @param int|string|null $defaultStoreId
	 * @return int
	 */
	protected function getWebsiteStoreId($customer, $defaultStoreId = null)
	{
		if ($customer->getWebsiteId() != 0 && empty($defaultStoreId)) {
			$storeIds = $this->storeManager->getWebsite($customer->getWebsiteId())->getStoreIds();
			reset($storeIds);
			$defaultStoreId = current($storeIds);
		}

		if (empty($defaultStoreId)) {
			$defaultStoreId = $this->storeManager->getDefaultStoreView()->getId();
		}

		return $defaultStoreId;
	}

	public function loadCmsBlock($blockIdentify, $title = false)
	{
		$html      = '';
		$titleHtml = '';
		if ($blockIdentify) {
			$block = $this->_blockFactory->create()
				->load($blockIdentify, 'identifier');
			if ($block->getIsActive()) {
				$titleHtml = $block->getTitle();
				$html      = $this->_layout->createBlock('Magento\Cms\Block\Block')->setBlockId($blockIdentify)->toHtml();
			}
		}

		if ($title) {
			return array(
				'title'   => $titleHtml,
				'content' => $html
			);
		}

		return $html;
	}

	public function getCustomerReferBy()
	{
		$key    = $this->getAffiliateKey();
		$account = $this->accountFactory->create()->loadByCode($key);

		if(!$account->getId()){
            $account = $this->accountFactory->create()->load($key);
        }

		if ($account->getId()) {
			return $this->getCustomerEmailByAccount($account);
		}

		return null;
	}

	public function getAffiliateByEmailOrCode($input)
	{
		$account = $this->accountFactory->create();

		$validator = new \Zend\Validator\EmailAddress();
		if ($validator->isValid($input)) {
			$websiteId = $this->storeManager->getStore()->getWebsiteId();
			$customer  = $this->customerFactory->create();
			$customer->setWebsiteId($websiteId)->loadByEmail($input);
			if ($customer && $customer->getId()) {
				$account->loadByCustomer($customer);
			}
		} else {
			$account->loadByCode($input);
		}

		return $account->getId();
	}

	public function getCustomerEmailByAccount($account)
	{
		$customerId = '';
		if (is_object($account)) {
			$customerId = $account->getCustomerId();
		} else {
			$account = $this->accountFactory->create()->load($account);
			if ($account->getId())
				$customerId = $account->getCustomerId();
		}

		$customer = $this->customerFactory->create()->load($customerId);
		if ($customer->getId()) {
			return $customer->getEmail();
		}

		return '';
	}

	public function getAffiliateKeyFromCookie($key = null)
	{
		if (is_null($key)) {
			$key = self::AFFILIATE_COOKIE_NAME;
		}

		if (!isset(self::$_key[$key])) {
			self::$_key[$key] = $this->cookieManager->getCookie($key);
		}

		return self::$_key[$key];
	}

	public function setAffiliateKeyToCookie($code, $key = null)
	{
		$expirationDay = (int)$this->getAffiliateConfig('general/expired_time');
		$period        = $expirationDay * 24 * 3600;
		if (is_null($key)) {
			$key = self::AFFILIATE_COOKIE_NAME;
		}

		if ($this->cookieManager->getCookie($key)) {
			$this->cookieManager->deleteCookie($key,
				$this->cookieMetadataFactory
					->createCookieMetadata()
					->setPath('/')
					->setDomain(null)
			);
		}

		$this->cookieManager->setPublicCookie($key, $code,
			$this->cookieMetadataFactory
				->createPublicCookieMetadata()
				->setDuration($period)
				->setPath('/')
				->setDomain(null)
		);

		self::$_key[$key] = $code;

		return $this;
	}

	public function deleteAffiliateKeyFromCookie($key = null)
	{
		if (is_null($key)) {
			$key = self::AFFILIATE_COOKIE_NAME;
		}

		if ($this->cookieManager->getCookie($key)) {
			$this->cookieManager->deleteCookie($key,
				$this->cookieMetadataFactory
					->createCookieMetadata()
					->setPath('/')
					->setDomain(null)
			);
		}

		self::$_key[$key] = null;

		return $this;
	}

	public function getSharingUrl($url = null, $params = array(), $urlType = null)
	{
		if (is_null($url)) {
			$url = $this->getAffiliateConfig('refer/default_link');
			if (!$url) {
				$url = $this->_urlBuilder->getUrl('affiliate/index/index');
			}
		}

		$prefix = $this->getAffiliateConfig('general/url/prefix');
		if (!$prefix) {
			$prefix = 'u';
		}

		$urlType = $urlType ?: $this->getAffiliateConfig('general/url/type');
		$accountCode = $this->getCurrentAffiliate()->getCode();

		if ($this->getAffiliateConfig('general/url/param') == \Mageplaza\Affiliate\Model\Config\Source\Urlparam::PARAM_ID) {
			$accountCode = $this->getCurrentAffiliate()->getId();
		}
		if ($urlType == \Mageplaza\Affiliate\Model\Config\Source\Urltype::TYPE_HASH) {
			$param = '#' . $prefix . $accountCode;

			return trim($url, '/') . $param;
		}

		$params[$prefix] = $accountCode;
		$param = '';
		foreach ($params as $key => $code) {
			$paramPrefix = ($param != '') ? '&' : '?';
			$param .= $paramPrefix . $key . '=' . urlencode($code);
		}

		return trim($url, '/') . $param;
	}

	public function getCustomer()
	{
		return $this->_customerSession->getCustomer();
	}

	public function getAffiliateUrl($router, $param = [])
	{
		return $this->_getUrl($router, $param);
	}

	public function formatPrice($price)
	{
		return $this->priceCurrency->convertAndFormat($price, false);
	}

	public function getCreditTitle()
	{
		$account = $this->getCurrentAffiliate();

		return __('My Credit (%1)', $this->formatPrice($account->getBalance()));
	}

	public function getCheckoutSession()
    {
        if (!$this->_checkoutSession) {
            $this->_checkoutSession = $this->objectManager->get($this->isAdmin() ? Quote::class : CheckoutSession::class);
        }

        return $this->_checkoutSession;
    }

    /**
     * Is Admin Store
     *
     * @return bool
     */
    public function isAdmin()
    {
        /** @var \Magento\Framework\App\State $state */
        $state = $this->objectManager->get('Magento\Framework\App\State');

        return $state->getAreaCode() == Area::AREA_ADMINHTML;
    }

    public function getAffiliateDiscount()
    {
        $affDiscountData = $this->getCheckoutSession()->getAffDiscountData();
        if (!is_array($affDiscountData) || $this->hasFirstOrder()) {
            $affDiscountData = [];
        }

        return $affDiscountData;
    }

    public function saveAffiliateDiscount($affiliateDiscount)
    {
        $affDiscountData = $this->getAffiliateDiscount();
        $this->getCheckoutSession()->setAffDiscountData(array_merge($affDiscountData, $affiliateDiscount));

        return $this;
    }

    public function getAffiliateByKeyOrCode($key)
    {
    	$account = $this->accountFactory->create()->loadByCode($key);
    	if (!$account->getId()) {
    		$account = $this->accountFactory->create()->load($key);
    	}

    	return $account;
    }
}
