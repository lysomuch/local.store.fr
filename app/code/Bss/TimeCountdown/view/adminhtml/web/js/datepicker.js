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
        "jquery", "jquery/ui", "mage/calendar"
    ],
    function($)
    {
        return function(datepicker){
            $(document).ready(function () {
                var id = datepicker.id;
                $('#'+id).calendar(
                    {
                        dateFormat:"yy-mm-dd",
                        showButtonPanel: true,
                        changeMonth: true,
                        changeYear: true,
                        yearRange: "-90:+00"
                    });
                $(".ui-datepicker-trigger").removeAttr("style");
                $(".control-value").hide();
                $(".ui-datepicker-trigger").click(function(){
                    $(this).prev().focus();
                });
            });
        }
    }
);
