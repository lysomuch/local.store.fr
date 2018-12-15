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
namespace Bss\TimeCountdown\Model\Config\Source\Widget;

class ColorPicker extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    protected $_elementFactory;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * ColorPicker constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ){
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
        $input->setClass("admin__control-text input-text no-changes _has-datepicker");
        if ($element->getRequired()) {
            $input->addClass('required-entry');
        }
        $value = $element->getData('value');
        $id = $element->getHtmlId();
        $addHtml = '';

        if ($this->getRequest()->isXmlHttpRequest()) {

            $addHtml .= '<script type="text/javascript">
           require(["jquery", "jquery/ui", "jquery/colorpicker/js/colorpicker"], function (jq) {
                jq(document).ready(function () {
                    var el = jq("#' . $id . '");
                    el.css("backgroundColor", "'. $value .'");
                    jq(".control-value").hide();
                    jq("#'.$id.'").click(function () {
                        jq(".colorpicker").css("z-index","1000");
                    })
                    
                    // Attach the color picker
                    el.ColorPicker({
                        color: "'. $value .'",
                        onChange: function (hsb, hex, rgb) {
                            el.css("backgroundColor", "#" + hex).val("#" + hex);
                        }
                    });
                    
                    var useMessStartTime = jq("select[name=\'parameters[enable_mess_start]").val();
                    var useMessEndTime = jq("select[name=\'parameters[enable_mess_end]\']").val();
                    if(useMessStartTime == 0) {
                        jq("input[name=\'parameters[font_color_start]\']").parent().parent().parent().hide();
                        jq("input[name=\'parameters[font_color_start]\']").parent().parent().hide();
                    }
                    if(useMessEndTime == 0) {
                        jq("input[name=\'parameters[font_color_end]\']").parent().parent().parent().hide();
                        jq("input[name=\'parameters[font_color_end]\']").parent().parent().hide();
                    }
    
                    
                        jq("select[name=\'parameters[enable_mess_start]").change(function(){
                            if(jq(this).val() == 0) {
                                jq("input[name=\'parameters[font_color_start]\']").parent().parent().parent().hide();
                                jq("input[name=\'parameters[font_color_start]\']").parent().parent().hide();
                            } else if(jq(this).val() == 1) {
                                jq("input[name=\'parameters[font_color_start]\']").parent().parent().parent().show();
                                jq("input[name=\'parameters[font_color_start]\']").parent().parent().show();
                            }
                        })
    
                        jq("select[name=\'parameters[enable_mess_end]").change(function(){
                            if(jq(this).val() == 0) {
                                console.log(\'Im here\');
                                jq("input[name=\'parameters[font_color_end]\']").parent().parent().parent().hide();
                                jq("input[name=\'parameters[font_color_end]\']").parent().parent().hide();
                            } else if(jq(this).val() == 1) {
                                jq("input[name=\'parameters[font_color_end]\']").parent().parent().parent().show();
                                jq("input[name=\'parameters[font_color_end]\']").parent().parent().show();
                            }
                        })
                    
                    
                    
                });
            });
            </script>';
        } else {
            $addHtml .= '<script type="text/x-magento-init">
                         {
                            "*":{
                                   "Bss_TimeCountdown/js/colorpicker":{
                                   "id": "'.$id.'",
                                   "value": "'.$value.'"
                                   }
                                }
                         }
                    </script>';
        }

        $html = $element->setData('after_element_html', $input->getElementHtml().$addHtml);
        return $html;
    }
}

