<?php
namespace Silk\Cms\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\PageFactory;

/**
 * Upgrade the CatalogStaging module DB scheme
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var BlockFactory
     */
    protected $blockFactory;

    /**
     * @var pageFactory
     */
    protected $pageFactory;

    /**
     * @param BlockFactory $modelBlockFactory
     */
    public function __construct(
        BlockFactory $modelBlockFactory,
        PageFactory $pageFactory
    ) {
        $this->blockFactory = $modelBlockFactory;
        $this->pageFactory = $pageFactory;
    }

    /**
     * Upgrades DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '0.0.2', '<')) {
            $cmsBlock = [
                [
                    'title' => 'NEWEST PRODUCT',
                    'identifier' => 'newest_product',
                    'content' => '
                        <div>
                            <a href="" title="">
                                <div><img src="" title=""/></div>
                                <div>
                                    {{block class="Silk\\Cms\\Block\\Product" sku="newest_product_1" name="newest_product_1" template="Silk_Cms::homepage/product.phtml"}}
                                </div>
                            </a>
                        </div>
                        <div>
                            <a href="" title="">
                                <div><img src="" title=""/></div>
                                <div>
                                    {{block class="Silk\\Cms\\Block\\Product" sku="newest_product_2" name="newest_product_2" template="Silk_Cms::homepage/product.phtml"}}
                                </div>
                            </a>
                        </div>
                        <div>
                            <a href="" title="">
                                <div><img src="" title=""/></div>
                                <div>
                                    {{block class="Silk\\Cms\\Block\\Product" sku="newest_product_3" name="newest_product_3" template="Silk_Cms::homepage/product.phtml"}}
                                </div>
                            </a>
                        </div>',
                    'is_active' => 1,
                    'stores' => 0,
                ],
                [
                    'title' => 'FLASHLIGHT BEST SELLER',
                    'identifier' => 'flashlight_best_seller',
                    'content' => '
                        <div>
                            <a href="" title="">
                                <div><img src="" title=""/></div>
                                <div>
                                    {{block class="Silk\\Cms\\Block\\Product" sku="flashlight_best_seller_1" name="flashlight_best_seller_1" template="Silk_Cms::homepage/product.phtml"}}
                                </div>
                            </a>
                        </div>
                        <div>
                            <a href="" title="">
                                <div><img src="" title=""/></div>
                                <div>
                                    {{block class="Silk\\Cms\\Block\\Product" sku="flashlight_best_seller_2" name="flashlight_best_seller_2" template="Silk_Cms::homepage/product.phtml"}}
                                </div>
                            </a>
                        </div>
                        <div>
                            <a href="" title="">
                                <div><img src="" title=""/></div>
                                <div>
                                    {{block class="Silk\\Cms\\Block\\Product" sku="flashlight_best_seller_3" name="flashlight_best_seller_3" template="Silk_Cms::homepage/product.phtml"}}
                                </div>
                            </a>
                        </div>
                        <div>
                            <a href="" title="">
                                <div><img src="" title=""/></div>
                                <div>
                                    {{block class="Silk\\Cms\\Block\\Product" sku="flashlight_best_seller_4" name="flashlight_best_seller_4" template="Silk_Cms::homepage/product.phtml"}}
                                </div>
                            </a>
                        </div>
                        <div>
                            <a href="" title="">
                                <div><img src="" title=""/></div>
                                <div>
                                    {{block class="Silk\\Cms\\Block\\Product" sku="flashlight_best_seller_4" name="flashlight_best_seller_4" template="Silk_Cms::homepage/product.phtml"}}
                                </div>
                            </a>
                        </div>',
                    'is_active' => 1,
                    'stores' => 0,
                ],
                [
                    'title' => 'WEAPONLIGHT BEST SELLER',
                    'identifier' => 'weaponlight_best_seller',
                    'content' => '
                        <div>
                            <a href="" title="">
                                <div><img src="" title=""/></div>
                                <div>
                                    {{block class="Silk\\Cms\\Block\\Product" sku="weaponlight_best_seller_1" name="weaponlight_best_seller_1" template="Silk_Cms::homepage/product.phtml"}}
                                </div>
                            </a>
                        </div>
                        <div>
                            <a href="" title="">
                                <div><img src="" title=""/></div>
                                <div>
                                    {{block class="Silk\\Cms\\Block\\Product" sku="weaponlight_best_seller_2" name="weaponlight_best_seller_2" template="Silk_Cms::homepage/product.phtml"}}
                                </div>
                            </a>
                        </div>
                        <div>
                            <a href="" title="">
                                <div><img src="" title=""/></div>
                                <div>
                                    {{block class="Silk\\Cms\\Block\\Product" sku="weaponlight_best_seller_3" name="weaponlight_best_seller_3" template="Silk_Cms::homepage/product.phtml"}}
                                </div>
                            </a>
                        </div>',
                    'is_active' => 1,
                    'stores' => 0,
                ],
                [
                    'title' => 'HEADLAMP BEST SELLER',
                    'identifier' => 'headlamp_best_seller',
                    'content' => '
                            <div>
                                <a href="" title="">
                                    <div><img src="" title=""/></div>
                                    <div>
                                        {{block class="Silk\\Cms\\Block\\Product" sku="headlamp_best_seller_1" name="headlamp_best_seller_1" template="Silk_Cms::homepage/product.phtml"}}
                                    </div>
                                </a>
                            </div>
                            <div>
                                <a href="" title="">
                                    <div><img src="" title=""/></div>
                                    <div>
                                        {{block class="Silk\\Cms\\Block\\Product" sku="headlamp_best_seller_2" name="headlamp_best_seller_2" template="Silk_Cms::homepage/product.phtml"}}
                                    </div>
                                </a>
                            </div>
                            <div>
                                <a href="" title="">
                                    <div><img src="" title=""/></div>
                                    <div>
                                        {{block class="Silk\\Cms\\Block\\Product" sku="headlamp_best_seller_3" name="headlamp_best_seller_3" template="Silk_Cms::homepage/product.phtml"}}
                                    </div>
                                </a>
                            </div>',
                    'is_active' => 1,
                    'stores' => 0,
                ],
                [
                    'title' => 'ACCESSORY BEST SELLER',
                    'identifier' => 'accessory_best_seller',
                    'content' => '
                        <div>
                            <a href="" title="">
                                <div><img src="" title=""/></div>
                                <div>
                                    {{block class="Silk\\Cms\\Block\\Product" sku="accessory_best_seller_1" name="accessory_best_seller_1" template="Silk_Cms::homepage/product.phtml"}}
                                </div>
                            </a>
                        </div>
                        <div>
                            <a href="" title="">
                                <div><img src="" title=""/></div>
                                <div>
                                    {{block class="Silk\\Cms\\Block\\Product" sku="accessory_best_seller_2" name="accessory_best_seller_2" template="Silk_Cms::homepage/product.phtml"}}
                                </div>
                            </a>
                        </div>
                        <div>
                            <a href="" title="">
                                <div><img src="" title=""/></div>
                                <div>
                                    {{block class="Silk\\Cms\\Block\\Product" sku="accessory_best_seller_3" name="accessory_best_seller_3" template="Silk_Cms::homepage/product.phtml"}}
                                </div>
                            </a>
                        </div>
                        <div>
                            <a href="" title="">
                                <div><img src="" title=""/></div>
                                <div>
                                    {{block class="Silk\\Cms\\Block\\Product" sku="accessory_best_seller_4" name="accessory_best_seller_4" template="Silk_Cms::homepage/product.phtml"}}
                                </div>
                            </a>
                        </div>
                        <div>
                            <a href="" title="">
                                <div><img src="" title=""/></div>
                                <div>
                                    {{block class="Silk\\Cms\\Block\\Product" sku="accessory_best_seller_5" name="accessory_best_seller_5" template="Silk_Cms::homepage/product.phtml"}}
                                </div>
                            </a>
                        </div>',
                    'is_active' => 1,
                    'stores' => 0,
                ],
            ];

            /** @var \Magento\Cms\Model\Block $block */
            $block = $this->blockFactory->create();
            foreach ($cmsBlock as $item) {
                $block->setData($item)->save();
            }
        }

        if (version_compare($context->getVersion(), '0.0.3', '<')) {
            $cmsBlock = [
                [
                    'title' => 'CMS Live',
                    'identifier' => 'cms_live',
                    'content' => '
                        <div class="flash_unstart_live">
                            <div class="position-content">
                                <div class="live-tit">
                                    <div class="head-icon">
                                    <span class="olight-image">
                                        <img src="{{media url="wysiwyg/live_head.png"}}" alt="" />
                                    </span>
                                    </div>
                                    <div class="head-info">
                                        <p class="live-name">Olight World</p>
                                        <div class="likes">
                                            <i class="fa fa-heart"></i>
                                            80k Likes
                                        </div>
                                    </div>
                        
                                </div>
                                <div class="live-list">
                                    <a class="live-item" href="#">
                                        <div class="date-info">
                                            <span class="month">Apr</span>
                                            <span class="day">27</span>
                                        </div>
                                        <div class="item-right">
                                            <p class="live-review">Olight Flashlight Friday Live Video!</p>
                                            <p class="location">Fri 12:30 PM EDT · Olight World · Marietta, GA, United States</p>
                                        </div>
                                    </a>
                                    <a class="live-item" href="#">
                                        <div class="date-info">
                                            <span class="month">Apr</span>
                                            <span class="day">27</span>
                                        </div>
                                        <div class="item-right">
                                            <p class="live-review">Olight Flashlight Friday Live Video!</p>
                                            <p class="location">Fri 12:30 PM EDT · Olight World · Marietta, GA, United States</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>',
                    'is_active' => 1,
                    'stores' => 0,
                ],
                [
                    'title' => 'Footer Links',
                    'identifier' => 'footer_links',
                    'content' => '
                        <div class="links">
                            <div class="block-title"><strong>Products</strong></div>
                            <ul>
                                <li><a href="/flashlights.html">Flashlights</a></li>
                                <li><a href="/headlamps.html">Headlamps</a></li>
                                <li><a href="/flashlights/hunting.html">Hunting</a></li>
                                <li><a href="/flashlights-accessories.html">Accessory</a></li>
                                <li><a href="/flashlights/outdoor-camping.html">Outdoor (future)</a></li>
                            </ul>
                        </div>
                        <div class="links">
                            <div class="block-title"><strong>SUPPORT</strong></div>
                            <ul>
                                <li><a href="/faq">FAQs</a></li>
                                <li><a href="/warranty">Warranty</a></li>
                                <li><a href="/privacy-security">Piracy & Security</a></li>
                                <li><a href="/returns-replacements">Returns & Replacements</a></li>
                            </ul>
                        </div>
                        <div class="links">
                            <div class="block-title"><strong>Company</strong></div>
                            <ul>
                                <li><a href="/about-us">About US</a></li>
                                <li style="display: none"><a href="#">Press Room (BLOG)</a></li>
                                <li><a href="/contact">Contact US</a></li>
                            </ul>
                        </div>
                        <div class="links">
                            <div class="block-title"><strong>Contact US</strong></div>
                            <ul>
                                <li><span>Customer service: 770-779-7156</span></li>
                                <li><span>Email: dealer@olightstore.com</span></li>
                                <li><span>Support: cs@olightstore.com</span></li>
                                <li><span>Open Hours: 8:00 a.m. – 5:00 p.m.
                                   EST Mon-Fr</span></li>
                               </ul>
                           </div>',
                    'is_active' => 1,
                    'stores' => 0,
                ],
                [
                    'title' => 'Home Banner',
                    'identifier' => 'home_banner',
                    'content' => '
                        <div class="banner">
                            <div id="pageContain">
                                <div class="page page1">
                                    <div class="contain">
                                        <div class="banner-bg" style="background-image: url(\'{{media url="wysiwyg/banner1.jpg"}}\');"></div>
                                    </div>
                                </div>
                                <div class="page page2">
                                    <div class="contain">
                                        <div class="banner-bg" style="background-image: url(\'{{media url="wysiwyg/banner2.jpg"}}\');"></div>
                                    </div>
                                </div>
                                <div class="page page3">
                                    <div class="contain">
                                        <div class="banner-bg" style="background-image: url(\'{{media url="wysiwyg/banner3.jpg"}}\');"></div>
                                    </div>
                                </div>
                                <ul id="navBar">
                                    <li>1</li>
                                    <li>2</li>
                                    <li>3</li>
                                </ul>
                            </div>
                        </div>',
                    'is_active' => 1,
                    'stores' => 0,
                ]

            ];

            /** @var \Magento\Cms\Model\Block $block */
            $block = $this->blockFactory->create();
            foreach ($cmsBlock as $item) {
                $block->setData($item)->save();
            }
        }

        if (version_compare($context->getVersion(), '0.0.4', '<')) {
            $cmsBlock = [
                [
                    'title' => 'Support Links',
                    'identifier' => 'support_links',
                    'content' => '
                        <ul>
                            <li>
                                <a href="{{config path="web/secure/base_url"}}about-us">
                                    <div class="img-box"><img src="{{media url="wysiwyg/live_head.png"}}" alt="" /></div>
                                    <div class="info-content"><span class="product-name">About Us</span></div>
                                </a>
                            </li>
                            <li>
                                <a href="{{config path="web/secure/base_url"}}warranty">
                                <div class="img-box"><img src="https://www.olightstore.com/image/catalog/design/olight-flashlights-logo.png" alt="" /></div>
                                <div class="info-content"><span class="product-name">Warranty</span></div>
                                </a>
                            </li>
                            <li>
                                <a href="{{config path="web/secure/base_url"}}returns-replacements">
                                <div class="img-box"><img src="https://www.olightstore.com/image/catalog/design/olight-flashlights-logo.png" alt="" /></div>
                                <div class="info-content"><span class="product-name">Returns & Replacements</span></div>
                                </a>
                            </li>
                            <li>
                                <a href="{{config path="web/secure/base_url"}}privacy-security">
                                <div class="img-box"><img src="https://www.olightstore.com/image/catalog/design/olight-flashlights-logo.png" alt="" /></div>
                                <div class="info-content"><span class="product-name">Privacy & Security</span></div>
                                </a>
                            </li>
                        </ul>',
                    'is_active' => 1,
                    'stores' => 0,
                ]
            ];

            /** @var \Magento\Cms\Model\Block $block */
            $block = $this->blockFactory->create();
            foreach ($cmsBlock as $item) {
                $block->setData($item)->save();
            }
        }

        if (version_compare($context->getVersion(), '0.0.5', '<')) {
            $cmsPage = [
                'title' => 'Dealers & Wholesale',
                'page_layout' => '1column',
                'identifier' => 'olight-dealers-and-wholesales',
                'content_heading' => 'Dealers & Wholesale',
                'content' => '
                    <div class="dealers-wholesale-wrapper">
                        <p><img src="{{media url="wysiwyg/dealer-olight.jpg"}}" alt="" /></p>
                        <p>Olight technology has taken pride in being at the forefront of illumination technology throughout the past decade. Our vision is to get the most advanced technology and performance into the hands of everyone across the world while maintaining a simplified and streamlined user experience. We would like you to join us in that Journey.</p>
                        <p>With our top in the industry technology, manufacturing, customer support, and 5-year warranty, there is no better time to be a part of the Olight family. We realize that we are here because of you. Your dedication to our brand is the reason why we have come so far over the past 10 years and we are growing at a rate that we could have only dreamed of a few years ago.</p>
                        <p>Our goal is to develop and maintain long term relationships with companies that share our same passion and commitment to illumination excellence. We want your customers to have the best flashlights and accessories out there and we can lead you there. If you share our same philosophy for excellence and providing your customers with the best products in the industry, please complete the form below.</p>
                        <p>We are proud to offer products to large orders and dealers from OLIGHT directly located in Marietta, GA. There are several advantages listed below:</p>
                        <p>- Buy directly from OlightStore.com B2B portal.<br /> - Access to brand new inventory immediately.<br /> - Products are shipped directly from our USA warehouse.<br /> - Express delivery 1-3 business day.<br /> - All customer service and warranty requests are fulfilled from USA branch and will be taken care of in a timely manner.</p>
                        <p>There are two dealer account types available:</p>
                        <ol>
                            <li>Bulk orders for companies ordering in large quantities (wholesales) for end user needs (E.g. police departments, employee gifts, security companies, firefighter departments, etc.)</li>
                            <li>Dealers selling Olight products in their stores and ordering new products on a regular basis.</li>
                        </ol>
                        <div class="form-box">
                            <div class="form-wrapper">
                                <h1>Request Dealer/Wholesale Account</h1>
                                <form id="form-validate" class="form-class" action="{{store url="cms/dealerwholesale/dosubmit"}}" enctype="multipart/form-data" method="post" data-mage-init="{&quot;validation&quot;:{}}">
                                <ul class="form-ul">
                                    <li class="li-text field company-name required"><label for="field-c2">Company Name <abbr>*</abbr></label><input id="field-c2" class="f-c2 required large" name="data[company_name]" type="text" value="" data-validate="{required:true}" /></li>
                                    <li class="li-text"><label for="field-c45">Login Password<abbr>*</abbr></label><input id="field-c45" class="f-c45 required large" name="data[login_password]" type="text" value="" placeholder="Desired login password" /></li>
                                    <li class="li-text"><label for="field-c14">Contact Name<abbr>*</abbr></label><input id="field-c14" class="f-c14 required large" name="data[contact_name]" type="text" value="" /></li>
                                    <li class="li-email"><label for="field-c48">Email<abbr>*</abbr></label><input id="field-c48" class="f-c48 required large" name="data[email]" type="text" value="" data-validate="{required:true, \'validate-email\':true}" /></li>
                                    <li class="li-text"><label for="field-c38">Website</label><input id="field-c38" class="f-c38 large" name="data[website]" type="text" value="" /></li>
                                    <li class="li-text"><label for="field-c17">Telephone<abbr>*</abbr></label><input id="field-c17" class="f-c17 required large" name="data[telephone]" type="text" value="" /></li>
                                    <li class="li-text"><label for="field-c23">State / City<abbr>*</abbr></label><input id="field-c23" class="f-c23 required large" name="data[city]" type="text" value="" /></li>
                                    <li class="li-paragraph"><label for="field-c31">Business Description<abbr>*</abbr></label><textarea id="field-c31" class="f-c31 required large" name="data[bussiness_description]" placeholder="Describe your business in a few words including mentions regarding what kind of products are you currently selling, number of physical stores and your primary audience."></textarea></li>
                                    <li class="li-text"><label for="field-c34">Do you currently sell flashlights and if yes, what brands?<abbr>*</abbr></label><input id="field-c34" class="f-c34 required large" name="data[flashlights_brands]" type="text" value="" /></li>
                                    <li class="li-checkboxes"><label for="field-c36">Sales Channels<abbr>*</abbr></label>
                                        <div class="checkbox-wrapper">
                                            <div class="label-box"><label for="field-c36-1"><input id="field-c36-1" class="f-c36 required" name="data[sales_channels][]" type="checkbox" value="Physical store(s)" /> Physical store(s)</label> <label for="field-c36-2"><input id="field-c36-2" class="f-c36 required" name="data[sales_channels][]" type="checkbox" value="Company Online Store" /> Company Online Store</label> <label for="field-c36-3"><input id="field-c36-3" class="f-c36 required" name="data[sales_channels][]" type="checkbox" value="Amazon" /> Amazon</label> <label for="field-c36-4"><input id="field-c36-4" class="f-c36 required" name="data[sales_channels][]" type="checkbox" value="Ebay" /> Ebay</label> <label for="field-c36-5"><input id="field-c36-5" class="f-c36 required" name="data[sales_channels][]" type="checkbox" value="Wholesale" /> Wholesale</label> <label for="field-c36-6"><input id="field-c36-6" class="f-c36 required" name="data[sales_channels][]" type="checkbox" value="other" /> Other</label></div>
                                            <input class="option-other" name="data[sales_channels][other_value]" type="text" value="" placeholder="Please enter Other" /></div>
                                    </li>
                                    <li class="li-paragraph"><label for="field-c40">Message to us</label><textarea id="field-c40" class="f-c40 large" name="data[message_to_us]"></textarea></li>
                                    <li class="li-submit"><input id="field-c6" class="action   primary" name="submit" type="submit" value="Submit Request" /></li>
                                </ul>
                                <input name="form_id" type="hidden" value="2" /></form></div>
                            <p></p>
                        </div>
                    </div>',
                'is_active' => 1,
                'stores' => [0],
                'sort_order' => 0
            ];

            $page = $this->pageFactory->create();
            $page->setData($cmsPage)->save();
        }

        $setup->endSetup();
    }
}