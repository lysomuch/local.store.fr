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
namespace Bss\TimeCountdown\Plugin;

Class ProductPlugin {

    /**
     * @var \Bss\TimeCountdown\Helper\Data
     */
    protected $helper;
    /**
     * @var \Magento\CatalogRule\Model\Rule
     */
    protected $rule;
    /**
     * @var \Bss\TimeCountdown\Helper\ModuleConfig
     */
    protected $helperConfig;
    /**
     * @var \Bss\TimeCountdown\Helper\ProductData
     */
    protected $helperProduct;

    /**
     * @var \Bss\TimeCountdown\Block\Catalog\TimerBlock
     */
    protected $timerBlock;

    /**
     * ProductPlugin constructor.
     * @param \Bss\TimeCountdown\Helper\Data $helper
     * @param \Bss\TimeCountdown\Helper\ModuleConfig $helperConfig
     * @param \Bss\TimeCountdown\Helper\ProductData $helperProduct
     * @param \Magento\CatalogRule\Model\Rule $rule
     */
    public function __construct(
        \Bss\TimeCountdown\Helper\Data $helper,
        \Bss\TimeCountdown\Helper\ModuleConfig $helperConfig,
        \Bss\TimeCountdown\Helper\ProductData $helperProduct,
        \Bss\TimeCountdown\Block\Catalog\TimerBlock $timerBlock,
        \Magento\CatalogRule\Model\Rule $rule
    ) {
        $this->helper=$helper;
        $this->helperConfig = $helperConfig;
        $this->helperProduct = $helperProduct;
        $this->rule = $rule;
        $this->timerBlock = $timerBlock;
    }

    /**
     * @param \Magento\Catalog\Block\Product\View $subject
     * @param $result
     * @return string
     * @throws \Zend_Db_Statement_Exception
     */
    public function afterToHtml(\Magento\Catalog\Block\Product\View $subject, $result)
    {
        $templateSetTimer = 'review.phtml';
        if(strpos($subject->getTemplate(),$templateSetTimer) !== false) {
            $product = $subject->getProduct();
            $infoDisplayProduct = $this->helperProduct->getInfoDisplayProductPage($product);
            $k = rand(1,100);
            $result = $this->timerBlock->setInfoDisplay($infoDisplayProduct)->setTimerTemplate('product')->setRandomKey($k)->toHtml() . $result;
            $numberSecond = $infoDisplayProduct['time_rest'];
            // if($infoDisplayProduct) {
            //     $result .= '
            //         <input type="hidden" class="product-id-bss-time" value="'.$infoDisplayProduct['product_id'].$k.'"/>
            //         <input type="hidden" id="time-product-bss-'.$infoDisplayProduct['product_id'].$k.'" value="'.$numberSecond.'"/>
            //         <p class="message-catalog-'.$infoDisplayProduct['type'].'-bss-style1 message-bss-'.$infoDisplayProduct['product_id'] . $k .'" style="'.$infoDisplayProduct['font_size'].'; '.$infoDisplayProduct['color'].'">'.$infoDisplayProduct['message'].'</p>
        
            //         <span class="timer-bss-style-'.$infoDisplayProduct['style'].' timer-countdown-bss-'.$infoDisplayProduct['product_id'].$k.' product"></span>';

            //     $result .= "<div class='discount-bss-time-countdown'><p style='".$infoDisplayProduct['corlorMessSaleValue'] .";".$infoDisplayProduct['fontSizeMessSaleValue']."'>".$infoDisplayProduct['messSaleValue']." {$infoDisplayProduct['sale_value']}</p><p style='".$infoDisplayProduct['corlorMessSalePercent'] .";".$infoDisplayProduct['fontSizeMessSalePercent']."'>".$infoDisplayProduct['messSalePercent']." {$infoDisplayProduct['percent_discount']}</p></div>";
            // }
            // $result .= '<script type="text/x-magento-init">
            //                  {
            //                     "*":{
            //                            "Bss_TimeCountdown/js/timer":{
            //                                "slector": ".timer-countdown-bss-'.$infoDisplayProduct['product_id'].$k.'",
            //                                "time": "'.$numberSecond.'",
            //                                "productId": "'.$infoDisplayProduct['product_id'].'"
            //                             }
            //                         }
            //                  }
            //             </script>';
        }
        return $result;
    }
}
