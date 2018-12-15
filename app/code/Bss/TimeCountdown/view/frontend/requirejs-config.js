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
var config = {
    config: {
        mixins: {
            "Magento_Swatches/js/swatch-renderer" : {
                "Bss_TimeCountdown/js/mixins/swatch-renderer": true
            }
        }
    },
    paths: {
        slick: 'Bss_TimeCountdown/js/slick/slick',
        pluginCountdown: 'Bss_TimeCountdown/js/timer/jquery.plugin',
        jqueryCountdown: 'Bss_TimeCountdown/js/timer/jquery.countdown'
    },
    shim: {
        slick: {
            deps: ['jquery']
        },
        pluginCountdown: {
            deps: ['jquery']
        },
        jqueryCountdown: {
            deps: ['jquery']
        }
    }
};
