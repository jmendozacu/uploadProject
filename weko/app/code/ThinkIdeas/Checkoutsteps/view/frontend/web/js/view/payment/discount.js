/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'Magento_SalesRule/js/action/set-coupon-code',
        'Magento_SalesRule/js/action/cancel-coupon'
    ],
    function ($, ko, Component, quote, setCouponCodeAction, cancelCouponAction) {
        'use strict';

        var totals = quote.getTotals(),
            couponCode = ko.observable(null),

            isApplied = ko.observable(couponCode() != null);

        if (totals()) {
            couponCode(totals()['coupon_code']);

        }

        //console.log(totals()['coupon_code']);return false;
        return Component.extend({
            defaults: {
                template: 'ThinkIdeas_Checkoutsteps/payment/discount'
            },
            couponCode: couponCode,

            /**
             * Applied flag
             */
            isApplied: isApplied,

            /**
             * Coupon code application procedure
             */
            apply: function() {
                if (this.validate()) {
                    setCouponCodeAction(couponCode(), isApplied);
                }
            },

            /**
             * Cancel using coupon
             */
            cancel: function() {
                if (this.validate()) {
                    couponCode('');
                    cancelCouponAction(isApplied);
                }
            },

            /**
             * Coupon form validation
             *
             * @returns {Boolean}
             */
            validate: function () {
                var form = '#discount-form';

                return $(form).validation() && $(form).validation('isValid');
            }
        });
    }
);
