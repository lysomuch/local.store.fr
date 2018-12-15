define([
    'jquery',
    'prototype',
    'Magento_Ui/js/modal/alert'
], function (jQuery, prototype, alert) {
    'use strict';

    return function (config, element) {
        config = config || {};

        var validate = function () {
            var form = '#edit_form';
            return jQuery(form).validation() && jQuery(form).validation('isValid');
        };

        var stopGenerate = false,
            exported = 0;

        var feedGenerate = function (progress, generateUrl, useAjax, page) {
            if (validate()) {
                var params = $('edit_form').serialize(true);
                params.page = page;

                new Ajax.Request(generateUrl, {
                    parameters: params,
                    onSuccess: function (transport) {
                        var response = transport.responseText;
                        if (response.isJSON()) {
                            response = response.evalJSON();

                            if (response.error) {
                                progress.html(response.error);
                            } else if (!stopGenerate && !response.isLastPage) {
                                exported += response.exported;
                                progress.html(exported + ' ' + jQuery.mage.__('products from') + ' ' + response.total + ' ' + jQuery.mage.__('exported'));
                                feedGenerate(progress, generateUrl, useAjax, ++page);
                            } else if (response.download) {
                                progress.html('<a href="' + response.download + '">' + jQuery.mage.__('Download') + '</a>');
                            }
                        }
                    }
                });
            }
        };

        var progressFeedGenerate = function (url, useAjax) {
            stopGenerate = false;

            var progress = alert({
                content: jQuery.mage.__('Initializing'),
                title: jQuery.mage.__('Progress'),
                buttons: [{
                    text: jQuery.mage.__('Close'),
                    class: 'action-primary action-accept',
                    click: function () {
                        this.closeModal(true);
                    }
                }]
            });

            progress.bind('alertclosed', function () {
                stopGenerate = true;
            });

            feedGenerate(progress, url, useAjax, 0);
        };

        jQuery(element).on('click', function (event) {
            exported = 0;
            progressFeedGenerate(config.ajaxUrl, config.ajax);
        });

        if (window.location.hash == "#forcegenerate") {
            exported = 0;
            window.location.hash = "";
            progressFeedGenerate(config.ajaxUrl, config.ajax);
        }
    };
});
