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
namespace Bss\TimeCountdown\Model\Config\Source;

use Magento\Framework\Registry;
use Magento\Backend\Block\Template;

Class DatePicker extends Template
{

    /**
     * @var Registry
     */
    protected $_coreRegistry;
    /**
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    protected $_elementFactory;

    /**
     * DatePicker constructor.
     * @param Template\Context $context
     * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory
     * @param Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        Registry $coreRegistry,
        array $data = []
    )
    {
        $this->_elementFactory = $elementFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return $this
     */
    public function prepareElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $input = $this->_elementFactory->create("text", ['data' => $element->getData()]);
        $input->setId($element->getId());
        $input->setForm($element->getForm());
        $input->setClass("admin__control-text input-text no-changes");
        if ($element->getRequired()) {
            $input->addClass('required-entry');
        }
        if (!$this->_coreRegistry->registry('datepicker_loaded')) {
            $this->_coreRegistry->registry('datepicker_loaded', 1);
        }

        $id = $element->getHtmlId();
        $addHtml = '';
        if ($this->getRequest()->isXmlHttpRequest()) {
            $addHtml .= '<button type="button" style="display:none;" class="ui-datepicker-trigger '
                .'v-middle"><span>Select Date</span></button>';
            // add datepicker with element by jquery
            $addHtml .= '<script type="text/javascript">
            require(["jquery", "jquery/ui", "mage/calendar"], function (jq) {
                jq(document).ready(function () {
                    jq("#' . $id . '").datepicker( { dateFormat: "yy-mm-dd" } );
                    
                    jq(".ui-datepicker-trigger").removeAttr("style");
                    jq(".ui-datepicker-trigger").click(function(){
                        jq(this).prev().focus();
                    });
                    
                    jq(".control-value").css({"display": "none"});
                });
            });
            </script>';
        } else {
            $addHtml .= '<script type="text/x-magento-init">
                         {
                            "*":{
                                   "Bss_TimeCountdown/js/datepicker":{
                                   "id": "'.$id.'"
                                   }
                                }
                         }
                    </script>';
        }
        $html = $element->setData('after_element_html', $input->getElementHtml().$addHtml);
        return $html;
    }
}
