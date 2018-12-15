<?php
namespace Silk\Socialshare\Block;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;
class Socialshare extends \Magebuzz\Socialshare\Block\Socialshare
{
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PricingHelper $PricingHelper,
        ScopeConfigInterface $scopeConfig,
        array $data = array()
    ) {
        parent::__construct($context, $coreRegistry, $PricingHelper, $scopeConfig, $data);
    }

    public function getFacebookButton()
    {
        $facebookID = $this->_scopeConfig->getValue('socialshare/facebook/fb_id', ScopeInterface::SCOPE_STORE);
        $displayFbCount = $this->_scopeConfig->getValue('socialshare/facebook/display_facebook_count', ScopeInterface::SCOPE_STORE);
        $facebookID = ($facebookID != "") ? $facebookID : '410311982797770';
        $count_button = ($displayFbCount == 1) ? 'button_count' : 'button';

        return '<div class="facebook_button social-button">
 <div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = \'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.1&appId=' . $facebookID . '&autoLogAppEvents=1\';
  fjs.parentNode.insertBefore(js, fjs);
}(document, \'script\', \'facebook-jssdk\'));</script>


  <div class="fb-share-button" data-href="' . $this->shareUrl . '" data-layout="' . $count_button . '" data-size="small" data-mobile-iframe="true">
	<a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=' . urlencode($this->shareUrl) . '&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Share</a>
</div>
  ';
    }

    public function getTwitterButton()
    {
        return '<div class="twitter_button social-button">
            <a href="https://twitter.com/intent/tweet" data-url="'. $this->shareUrl .'" class="twitter-share-button" data-show-count="false">Twitter</a>
            <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
        </div>';
    }

}