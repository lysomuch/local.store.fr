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
    'jquery',
    'underscore',
    'jquery/ui',
    'jquery/jquery.parsequery'
], function ($, _) {
    'use strict';
    return function (widget) {

        $.widget('mage.SwatchRenderer', widget, {

            /**
             * Event for swatch options
             *
             * @param {Object} $this
             * @param {Object} $widget
             * @private
             */
            _OnClick: function ($this, $widget) {

                $widget._super($this, $widget);

                $widget._UpdateDetailProduct();
            },

            /**
             * Event for select
             *
             * @param {Object} $this
             * @param {Object} $widget
             * @private
             */
            _OnChange: function ($this, $widget) {

                $widget._super($this, $widget);

                $widget._UpdateDetailProduct();
            },

            _UpdateDetailProduct: function () {
                var $widget = this,
                    index = '',
                    options = _.object(_.keys($widget.optionsMap), {}),
                    childProductData = this.options.jsonConfig.demomixins;

                $widget.element.find('.' + $widget.options.classes.attributeClass + '[option-selected]').each(function () {
                    var attributeId = $(this).attr('attribute-id');
                    options[attributeId] = $(this).attr('option-selected');
                });

                index = _.findKey($widget.options.jsonConfig.index, options);

                if (!childProductData['child'].hasOwnProperty(index)) {
                    return false;
                }
                var timeCountdown = childProductData['child'][index]['timeCountdown'];
                $('.bss-time-countdown').remove();
                $('.discount-bss-time-countdown').remove();
                $('#product-id-bss-time-product').remove();
                if(timeCountdown) {
                    var productId = childProductData['child'][index]['entity']
                    var type = timeCountdown['type'];
                    var productStyle = timeCountdown['style'];
                    var message = timeCountdown['message'];
                    var color = timeCountdown['color'];
                    var font_size = timeCountdown['font_size'];

                    var messSaleValue = timeCountdown['messSaleValue'];
                    var corlorMessSaleValue = timeCountdown['corlorMessSaleValue'];
                    var fontSizeMessSaleValue = timeCountdown['fontSizeMessSaleValue'];
                    var messSalePercent = timeCountdown['messSalePercent'];

                    var corlorMessSalePercent = timeCountdown['corlorMessSalePercent'];
                    var fontSizeMessSalePercent = timeCountdown['fontSizeMessSalePercent'];


                    var day = timeCountdown['index_time']['day'];
                    var hour = timeCountdown['index_time']['hour'];
                    var minute = timeCountdown['index_time']['minute'];
                    var second = timeCountdown['index_time']['second'];

                    if(day < 10) {
                        day = '0'+day;
                    }
                    if(hour < 10) {
                        hour = '0'+hour;
                    }
                    if(minute < 10) {
                        minute = '0'+minute;
                    }
                    if(second < 10) {
                        second = '0'+second;
                    }

                    var sale_value = timeCountdown['sale_value'];
                    var percent_discount = timeCountdown['percent_discount'];

                    $('.product-options-wrapper').append('<input type="hidden" id="product-id-bss-time-product" value="' + productId + '"/><div class = "bss-time-countdown"><p class="message-catalog-' + type + '-bss-style1" style="' + font_size + 'px; ' + color + '">' + message + '</p><div class="time-count-down-bss-'+ productStyle +' product-bss"><table><tr><td><span class="num-day-' + type + ' num " id = "num-day-' + productId + '">'+day+'</span><span class="day-' + type + ' suffix-bss">Day</span></td><td><span class="num-hour-' + type + ' num" id = "num-hour-' + productId + '">'+hour+'</span><span class="hour-' + type + ' suffix-bss">Hour</span></td><td><span class="num-minute-' + type + ' num" id = "num-minute-' + productId + '">'+minute+'</span><span class="minute-' + type + ' suffix-bss">Min</span></td><td><span class="num-second-' + type + ' num " id = "num-second-' + productId + '">'+second+'</span><span class="second-' + type + ' suffix-bss">Sec</span></td></tr></table><input type="hidden" id="d-' + productId + '" value="' + day + '"/><input type="hidden" id="h-'+ productId +'" value="' + hour + '"/><input type="hidden" id="m-'+ productId +'" value="' + minute + '"/><input type="hidden" id="s-'+ productId +'" value="' + second +'"/></div></div><div class="discount-bss-time-countdown"><p style="'+corlorMessSaleValue+';' + fontSizeMessSaleValue + '">' + messSaleValue + '<span class="sale-value">'+sale_value+'</span></p><p style="'+corlorMessSalePercent+';' + fontSizeMessSalePercent + '">' + messSalePercent + '<span>&nbsp;('+percent_discount + ')</dpan></p></div>');
                    var timeoutStart;
                    var id = productId;

                    var d_start = parseInt(jQuery('#num-day-'+id).text());
                    var h_start = parseInt(jQuery('#num-hour-'+id).text());
                    var m_start = parseInt(jQuery('#num-minute-'+id).text());
                    var s_start = parseInt(jQuery('#num-second-'+id).text());

                    var dt_start, ht_start, mt_start, st_start;
                    var mark = true;
                    clearInterval(timeoutStart);
                    timeoutStart = setInterval(function () {
                        s_start--;

                        if (s_start == -1) {
                            mark = true;
                            m_start -= 1;
                            s_start = 59;
                        }

                        if (m_start == -1) {
                            mark = true;
                            h_start -= 1;
                            m_start = 59;
                        }

                        if (h_start == -1) {
                            mark = true;
                            d_start -= 1;
                            h_start = 23;
                        }

                        if (d_start == -1) {
                            clearInterval(timeoutStart);
                            return false;
                        }

                        timeCountdown['index_time']['day'] = d_start;
                        timeCountdown['index_time']['hour'] = h_start;
                        timeCountdown['index_time']['minute'] = m_start;
                        timeCountdown['index_time']['second'] = s_start;

                        if (d_start < 10) {
                            dt_start = '0' + d_start;
                        } else {
                            dt_start = d_start;
                        }
                        if (h_start < 10) {
                            ht_start = '0' + h_start;
                        } else {
                            ht_start = h_start;
                        }
                        if (m_start < 10) {
                            mt_start = '0' + m_start;
                        } else {
                            mt_start = m_start;
                        }
                        if (s_start < 10) {
                            st_start = '0' + s_start;
                        } else {
                            st_start = s_start;
                        }

                        if(mark) {
                            mark = false;
                            jQuery('#num-day-'+id).text(dt_start);
                            jQuery('#num-hour-'+id).text(ht_start);
                            jQuery('#num-minute-'+id).text(mt_start);
                        }
                        jQuery('#num-second-'+id).text(st_start);
                    }, 1000);
                }
            },
        });
        return $.mage.SwatchRenderer;
    }
});
