<?php
/**
* @copyright Copyright (c) 2016 www.magebuzz.com
*/

namespace Magebuzz\Socialshare\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;

class Socialshare extends \Magento\Framework\View\Element\Template
{
    public $_coreRegistry;
    public $_scopeConfig;
    public $_product;
    public $_priceHelper;
    public $shareUrl;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PricingHelper $PricingHelper,
        ScopeConfigInterface $scopeConfig,
        array $data = array()
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_priceHelper = $PricingHelper;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
        $this->_product = $this->_coreRegistry->registry('current_product');

        $this->shareUrl = $this->_product ? $this->_product->getProductUrl() : $this->_storeManager->getStore()->getCurrentUrl();
    }

    public function getShareDescription()
    {
        $productsData = '';
        $_product = $this->_product;
        if( ! $_product) return $productsData;

        $productName = $_product->getName();
        $productPrice = strip_tags($this->_priceHelper->currency(round($_product->getPrice(), 2)));
        $limit = $this->_scopeConfig->getValue('socialshare/whatsapp/description_limit', ScopeInterface::SCOPE_STORE);
        $productShortdesc = substr(strip_tags($_product->getDescription()), 0, $limit);
        $productsData = $this->_scopeConfig->getValue('socialshare/whatsapp/share_desc', ScopeInterface::SCOPE_STORE);
        $productsData = nl2br($productsData);
        $productsData = str_replace(array("[product-title]", "[product-price]", "[product-description]"), array($productName, $productPrice, $productShortdesc), $productsData);
        $productsData = str_replace(array("<br>", "<br/>", "<br />"), array("%0a", "%0a", "%0a"), $productsData);

        return $productsData;
    }

    public function getFacebookButton()
    {
        $facebookID = $this->_scopeConfig->getValue('socialshare/facebook/fb_id', ScopeInterface::SCOPE_STORE);
        $displayLike = $this->_scopeConfig->getValue('socialshare/facebook/display_onlylike', ScopeInterface::SCOPE_STORE);
        $displayFbCount = $this->_scopeConfig->getValue('socialshare/facebook/display_facebook_count', ScopeInterface::SCOPE_STORE);
        $facebookID = ($facebookID != "") ? $facebookID : '1082368948492595';
        $like_button = ($displayLike == 1) ? false : true;
        $count_button = ($displayFbCount == 1) ? 'button_count' : 'button';

        return '<div class="facebook_button social-button">
 <div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5&appId=' . $facebookID .'";
  fjs.parentNode.insertBefore(js, fjs);
}(document, \'script\', \'facebook-jssdk\'));</script>

  <div class="fb-like" data-layout="' . $count_button . '" data-width="400" data-show-faces="false"  data-href="' . $this->shareUrl . '"  data-send="' . $like_button . '"></div></div>';
    }

    public function getTwitterButton()
    {
        return "<div class='twitter_button social-button'>
  <a href='https://twitter.com/share' class='twitter-share-button' data-url='" . $this->shareUrl . "' >Tweet</a>
  <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script></div>";
    }

    public function getPinItButton()
    {
        $count_button = $this->_scopeConfig->getValue('socialshare/pinitsharing/display_pinit_count', ScopeInterface::SCOPE_STORE);
        $count_button = ($count_button == 1) ? 'beside' : 'none';

        return '<div class="pinit_button social-button">
  <a href="//www.pinterest.com/pin/create/button/?url=' . urlencode($this->shareUrl) . '&description=' . ($this->_product ? urlencode($this->_product->getName()) : '') . '" data-pin-do="buttonPin" data-pin-color="red" data-pin-config="' . $count_button . '"  data-pin-height="20">pinit</a>
  </div>
  <script type="text/javascript" async defer src="//assets.pinterest.com/js/pinit.js"></script>';
    }

    public function getGooglePlusButton()
    {
        $count_button = $this->_scopeConfig->getValue('socialshare/googleplus/display_google_count', ScopeInterface::SCOPE_STORE);
        $count_button = ($count_button == 1) ? 'bubble' : 'none';
        return '<div class="google_button social-button">
  <div class="g-plusone" data-size="medium"  data-annotation="' . $count_button . '"></div>
  </div>
  <script src="https://apis.google.com/js/platform.js" async defer></script>';
    }
}