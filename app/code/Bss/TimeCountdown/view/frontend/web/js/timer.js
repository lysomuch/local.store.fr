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
        "jquery",
        "pluginCountdown",
        "jqueryCountdown"
    ], function($) {
        $.widget('bss.timeCountdown', {
            _create: function () {
                var $widget = this;
                if ($widget.options.productId && $widget.options.type) {
                    $.ajax({
                        type: 'post',
                        url: $widget.options.ajaxUrl,
                        data: {
                            product_id: $widget.options.productId,
                            display_type: $widget.options.type
                        },
                        dataType: 'json',
                        success : function (time) {
                            $widget.element.val(time);
                            $($widget.options.selector).countdown({labels: ['Years', 'Months', 'Weeks', 'Days', 'Hour', 'Minute', 'Second'],until: time, format: 'dHMS', padZeroes: true});
                        },
                    });
                } else {
                    $.ajax({
                        type: 'post',
                        url: $widget.options.ajaxUrl,
                        data: {
                            time_rest: $widget.options.time
                        },
                        dataType: 'json',
                        success : function (time) {
                            $widget.element.val(time);
                            $($widget.options.selector).countdown({labels: ['Years', 'Months', 'Weeks', 'Days', 'Hour', 'Minute', 'Second'],until: time, format: 'dHMS', padZeroes: true});
                        },
                    });
                }
                
            }
        });

        return $.bss.timeCountdown;
    }
);