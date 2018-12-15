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
define([
        "jquery", "jquery/colorpicker/js/colorpicker"
    ],
    function($)
    {
        return function(colorpicker){
            var id = colorpicker.id;
            var value = colorpicker.value;
            $(document).ready(function () {
                var $el = $("#"+id);
                $el.css("backgroundColor", value);
                $(".control-value").hide();
                // Attach the color picker
                $el.ColorPicker({
                    color: value,
                    onChange: function (hsb, hex, rgb) {
                        $el.css("backgroundColor", "#" + hex).val("#" + hex);
                    }
                });

                var useMessStartTime = $("select[name='parameters[enable_mess_start]").val();
                var useMessEndTime = $("select[name='parameters[enable_mess_end]']").val();
                if(useMessStartTime == 0) {
                    $("input[name='parameters[font_color_start]']").parent().parent().parent().hide();
                    $("input[name='parameters[font_color_start]']").parent().parent().hide();
                }
                if(useMessEndTime == 0) {
                    $("input[name='parameters[font_color_end]']").parent().parent().parent().hide();
                    $("input[name='parameters[font_color_end]']").parent().parent().hide();
                }

                $(document).ready(function () {
                    $("select[name='parameters[enable_mess_start]").change(function(){
                        if($(this).val() == 0) {
                            $("input[name='parameters[font_color_start]']").parent().parent().parent().hide();
                            $("input[name='parameters[font_color_start]']").parent().parent().hide();
                        } else if($(this).val() == 1) {
                            $("input[name='parameters[font_color_start]']").parent().parent().parent().show();
                            $("input[name='parameters[font_color_start]']").parent().parent().show();
                        }
                    })

                    $("select[name='parameters[enable_mess_end]").change(function(){
                        if($(this).val() == 0) {
                            console.log('Im here');
                            $("input[name='parameters[font_color_end]']").parent().parent().parent().hide();
                            $("input[name='parameters[font_color_end]']").parent().parent().hide();
                        } else if($(this).val() == 1) {
                            $("input[name='parameters[font_color_end]']").parent().parent().parent().show();
                            $("input[name='parameters[font_color_end]']").parent().parent().show();
                        }
                    })
                });


            });
        }
    }
);