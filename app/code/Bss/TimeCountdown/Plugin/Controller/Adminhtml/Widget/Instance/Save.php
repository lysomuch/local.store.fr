<?php
/**
 * Bss Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   Bss
 * @package    Bss_TimeCountdown
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 Bss Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace  Bss\TimeCountdown\Plugin\Controller\Adminhtml\Widget\Instance;

class Save extends \Magento\Widget\Controller\Adminhtml\Widget\Instance\Save
{
    private $redirectFactory;
    function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Math\Random $mathRandom,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
    )
    {
        $this->redirectFactory = $redirectFactory;
        parent::__construct($context, $coreRegistry, $widgetFactory, $logger, $mathRandom, $translateInline);
    }

    /**
     * @param $subject
     * @param $proceed
     * @return Save
     */
    function aroundExecute($subject, $proceed) {
        $error[0] = false;
        if($this->getRequest()->getParam('code') === "bss_timecountdown"){
            $value = $this->getRequest()->getParam('parameters');
            $error1 = $this->validate_widget_clock_time($value,$proceed);
            $error = array_merge($error, $error1);
        }


         if($this->getRequest()->getParam('code') === "bss_timecountdown_list_product_onsale"){
            $value = $this->getRequest()->getParam('parameters');
             $error2 = $this->validate_widget_onsale($value,$proceed);
             $error = array_merge($error, $error2);
        }


        if($this->getRequest()->getParam('code') === "bss_timecountdown_list_product_comming_sale"){
            $value = $this->getRequest()->getParam('parameters');
            $error3 = $this->validate_widget_comming_sale($value,$proceed);
            $error = array_merge($error, $error3);
        }
        $errors = false;
            foreach ($error as $er) {
                if($er) {
                    $errors = true;
                }
            }
        return $this->checker($errors, $proceed);
    }

    /**
     * @param $value
     * @return array
     */
    protected function validate_widget_clock_time($value,$proceed) {
        $error = [];
        $fromDate = $value['from_date'];
        $toDate = $value['to_date'];
        $enable_mess_start = $value['enable_mess_start'];
        $enable_end_time = $value['enable_mess_end'];

        $messageFontSizeStart = __('Please input Font size start not empty or input must be type of integer');
        $messageFontSizeEnd = __('Please input Font size end not empty or input must be type of integer');
        $messageFromDate = __('Please input From Date field correct YY-MM-DD format');
        $messageToDate = __('Please input To Date field correct YY-MM-DD format');

        if($enable_mess_start) {
            $fontSizeStart = $value['font_size_start'];
            $error[] = $this->intGreatThanZero($fontSizeStart,$messageFontSizeStart, $proceed);
        }
        if($enable_end_time) {
            $fontSizeEnd = $value['font_size_end'];
            $error[] = $this->intGreatThanZero($fontSizeEnd,$messageFontSizeEnd, $proceed);
        }
        $error[] = $this->validDate($fromDate,$messageFromDate, $proceed);
        $error[] = $this->validDate($toDate,$messageToDate, $proceed);
        return $error;
    }

    /**
     * @param $value
     * @return array
     */
    protected function validate_widget_onsale ($value,$proceed) {
        $error = [];
        $show_slide_onsale = $value['show_slide_onsale'];
        $show_pager = isset($value['show_pager']) ? $value['show_pager'] : '';
        $products_count = $value['products_count'];
        $time_auto_slide = isset($value['time_auto_slide_onsale']) ? $value['time_auto_slide_onsale'] : '';
        $productPerPageSlide = isset($value['products_per_slide_onsale']) ? $value['products_per_slide_onsale'] : '';
        $products_per_page = isset($value['products_per_page']) ? $value['products_per_page'] : '';

        if($show_slide_onsale) {

            $messageProductPerPageSlide = __('Please input Number of Products per slide not empty or input must be type of integer and great than 0');
            $messageTimeAutoSlide = __('Please input Time auto slide not empty or input must be type of integer');
            $error[] = $this->intGreatThanZero($productPerPageSlide,$messageProductPerPageSlide, $proceed);
            $error[] = $this->intGreatEqualZero($time_auto_slide,$messageTimeAutoSlide, $proceed);

        }
        if($show_pager) {
            $messageProductsPerPage = __('Please input Number of Products per Page not empty or input must be type of integer and great than 0');
            $error[] = $this->intGreatThanZero($products_per_page,$messageProductsPerPage, $proceed);
        }
        $messageProductsCount = __('Please input Number of Products to Display not empty or input must be type of integer and great than 0');
        $error[] = $this->intGreatThanZero($products_count,$messageProductsCount, $proceed);
        return $error;
    }

    /**
     * @param $value
     * @return array
     */
    protected function validate_widget_comming_sale ($value,$proceed) {
        $error = [];
        $show_slide = $value['show_slide'];
        $show_pager = isset($value['show_pager']) ? $value['show_pager'] : '';
        $products_count = $value['products_count'];
        $time_auto_slide = isset($value['time_auto_slide']) ? $value['time_auto_slide'] : '';
        $productPerPageSlide = isset($value['products_per_slide']) ? $value['products_per_slide'] : '';
        $products_per_page = isset($value['products_per_page']) ? $value['products_per_page'] : '';

        if($show_slide) {
            $messageProductPerPageSlide = __('Please input Number of Products per slide not empty or input must be type of integer and great than 0');
            $messageTimeAutoSlide = __('Please input Time auto slide not empty or input must be type of integer and great than 0');
            $error[] = $this->intGreatThanZero($productPerPageSlide,$messageProductPerPageSlide, $proceed);
            $error[] = $this->intGreatEqualZero($time_auto_slide,$messageTimeAutoSlide, $proceed);
        }

        if($show_pager) {
            $messageProductsPerPage = __('Please input Number of Products per Page not empty or input must be type of integer and great than 0');
            $error[] = $this->intGreatThanZero($products_per_page,$messageProductsPerPage, $proceed);

        }
        $messageProductsCount = __('Please input Number of Products to Display not empty or input must be type of integer and great than 0');
        $error[] = $this->intGreatThanZero($products_count,$messageProductsCount, $proceed);
        return $error;
    }

    /**
     * @param null $value
     * @param $message
     * @param $proceed
     * @return bool
     */
    protected function intGreatThanZero ($value = null, $message, $proceed) {
        if(!$this->validate_int($value) || ($value <= 0)) {
            $this->messageManager->addErrorMessage(__($message));
            $this->checker(true, $proceed);
            return true;
        }
        return false;
    }


    /**
     * @param null $value
     * @param $message
     * @param $proceed
     * @return bool
     */
    protected function intGreatEqualZero ($value = null, $message,$proceed) {
        if(!$this->validate_int($value) || ($value < 0)) {
            $this->messageManager->addErrorMessage(__($message));
            $this->checker(true, $proceed);
            return true;
        }
        return false;
    }

    /**
     * @param null $value
     * @param $message
     * @param $proceed
     * @return bool
     */
    protected function validDate ($value = null, $message, $proceed) {
        if(!$this->dateValidate($value) ){
            $this->messageManager->addErrorMessage(__($message));
            $this->checker(true, $proceed);
            return true;
        }
        return false;
    }

    /**
     * @param $error
     * @param $proceed
     * @return $this
     */
    protected function checker($error, $proceed){
        if($error){
            return $this->redirectFactory->create()
                ->setPath('adminhtml/*/edit', ['_current' => true]);
        }
        else{
            return $proceed();
        }
    }

    /**
     * @param $int
     * @return bool
     */
    protected function validate_int($int) {
        $pattern = "/^[0-9]{1,11}$/";
        if (preg_match($pattern, $int)) {
            return true;
        }
        return false;
    }

    /**
     * @param $date
     * @return bool
     */
    function dateValidate($date)
    {
        // match the format of the date
        if (preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $date, $parts)) {
            // check whether the date is valid or not
            if (checkdate($parts[2], $parts[3], $parts[1])) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}