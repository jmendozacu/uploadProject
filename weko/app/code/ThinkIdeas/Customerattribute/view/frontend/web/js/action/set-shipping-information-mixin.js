/*jshint browser:true jquery:true*/
/*global alert*/
define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote'
], function ($, wrapper, quote) {
    'use strict';

    return function (setShippingInformationAction) {

        return wrapper.wrap(setShippingInformationAction, function (originalAction) {
            var shippingAddress = quote.shippingAddress();
            if (shippingAddress['extension_attributes'] === undefined) {
                shippingAddress['extension_attributes'] = {};
            }
            //console.log(typeof shippingAddress.customAttributes != undefined);            
            if(typeof shippingAddress.customAttributes !== 'undefined'){
                shippingAddress['extension_attributes']['weko_card_number'] = shippingAddress.customAttributes['weko_card_number'];
           
                shippingAddress['extension_attributes']['dob'] = shippingAddress.customAttributes['dob'];
            }
            // pass execution to original action ('Magento_Checkout/js/action/set-shipping-information')
            return originalAction();
        });
    };
});
