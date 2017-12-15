/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'Magento_Tax/js/view/checkout/summary/grand-total',
        'Magento_Checkout/js/model/totals'
    ],
    function (Component,totalsTax) {
        'use strict';

        return Component.extend({

            /**
             * @override
             */
            isDisplayed: function () {
                return true;
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
