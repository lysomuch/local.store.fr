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
        "jquery", "jquery/ui"
    ],
    function($)
    {
        return function(depenshowslide){
            $(document).ready(function () {
                var name_show_slide = depenshowslide.name_show_slide;
                var show_page;
                show_page = $("select[name='parameters[show_pager]']").val();
                if (show_page == 0) {
                    $("input[name='parameters[products_per_page]']").parent().parent().parent().hide();
                    $("input[name='parameters[products_per_page]']").parent().parent().hide();
                }

                $(".control-value").hide();
                $(name_show_slide).change(function () {
                    var selectedParams;
                    selectedParams = $(name_show_slide).val();
                    if (selectedParams == 1) {
                        $("input[name='parameters[products_per_page]']").parent().parent().parent().hide();
                        $("input[name='parameters[products_per_page]']").parent().parent().hide();
                        $("input[name='parameters[products_per_page]']").hide();
                        $("input[name='parameters[products_per_page]']").addClass("ignore-validate");
                    }
                    else {
                        var selectedParams2;
                        selectedParams2 = $("select[name='parameters[show_pager]']").val();
                        if(selectedParams2 == 1){
                            $("input[name='parameters[products_per_page]']").parent().parent().parent().show();
                            $("input[name='parameters[products_per_page]']").parent().parent().show();
                            $("input[name='parameters[products_per_page]']").show();
                            $("input[name='parameters[products_per_page]']").removeClass("ignore-validate");
                        }
                    }
                });
                $("select[name='parameters[show_pager]']").change(function () {
                    var selectedParams;
                    selectedParams = $("select[name='parameters[show_pager]']").val();
                    if (selectedParams == 0) {
                        $("input[name='parameters[products_per_page]']").parent().parent().parent().hide();
                        $("input[name='parameters[products_per_page]']").parent().parent().hide();
                    }
                    else {
                        $("input[name='parameters[products_per_page]']").parent().parent().parent().show();
                        $("input[name='parameters[products_per_page]']").parent().parent().show();
                    }
                });
            });
        }
    }
);
