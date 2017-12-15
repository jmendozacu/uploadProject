/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/totals'
    ],
    function (Component, quote, totalsTax) {
        "use strict";

        return Component.extend({
            defaults: {
                template: 'Magento_Checkout/summary/grand-total'
            },
            isDisplayed: function() {
                return this.isFullMode();
            },
            getPureValue: function() {
                var totals = quote.getTotals()();
                if (totals) {
                    return totals.grand_total;
                }
                return quote.grand_total;
            },
            getValue: function() {
                return this.getFormattedPrice(this.getPureValue());
            },

            getTaxvalue: function(){
                var isTax = this.getFormattedPrice(totalsTax.getSegment('tax').value);
                if(isTax){
                    return 'Inkl. MwSt. (=' + isTax + ')'
                }
            }
        });
    }
);
