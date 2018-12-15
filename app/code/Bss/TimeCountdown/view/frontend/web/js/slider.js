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
        "jquery", "jquery/ui", "slick"
    ],
    function($)
    {
        return function(slider){
            var id_slider = slider.id_slider;
            var autoslide = slider.autoslide;
            var time_auto_slide = slider.time_auto_slide;
            var product_per_slide = slider.product_per_slide;
            if(autoslide == 'true') {
                autoslide = true;
            } else {
                autoslide = false;
            }
            $(document).ready(function () {
                jQuery("."+id_slider).not('.slick-initialized').slick({
                    dots: true,
                    autoplay: autoslide,
                    autoplaySpeed: time_auto_slide * 1,
                    slidesToShow: product_per_slide*1,
                    slidesToScroll: product_per_slide*1,
                    responsive: [
                        {
                            breakpoint: 1194,
                            settings: {
                                slidesToShow: 4,
                                slidesToScroll: 4,
                            }
                        },
                        {
                            breakpoint: 1024,
                            settings: {
                                slidesToShow: 3,
                                slidesToScroll: 3,
                            }
                        },
                        {
                            breakpoint: 600,
                            settings: {
                                slidesToShow: 2,
                                slidesToScroll: 2,
                            }
                        },
                        {
                            breakpoint: 480,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1,
                            }
                        }
                    ]
                });
            });
        }
    }
);
