/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'Magento_Tax/js/view/checkout/summary/shipping',
        'Magento_Checkout/js/model/quote',
        'jquery',
    ],
    function (Component, quote,$) {
        'use strict';

        return Component.extend({

            /**
             * @override
             */
            isCalculated: function () {
                return !!quote.shippingMethod();
            },

            /**
             * @override
             */
            getShippingMethodTitle: function () {
                $("#free-shiippiing-text").css("display", "block");;
                $("#free-shiippiing-text").appendTo("#free-shipping-html");
                return '(' + this._super() + ')';
            }
        });
    }
);
