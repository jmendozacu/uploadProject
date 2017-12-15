/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Checkout/js/model/sidebar'
    ],
    function($, Component, quote, stepNavigator, sidebarModel) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'ThinkIdeas_Checkoutsteps/billing-information'
            },

            isVisible: function() {
                return !quote.isVirtual() && stepNavigator.isProcessed('billing');
            },

            getShippingMethodTitle: function() {
                var shippingMethod = quote.shippingMethod();
                return shippingMethod ? shippingMethod.carrier_title + " - " + shippingMethod.method_title : '';
            },

            back: function() {
                sidebarModel.hide();
                stepNavigator.navigateTo('billing');
            },

            backToShippingMethod: function() {
                sidebarModel.hide();
                stepNavigator.navigateTo('billing', 'opc-shipping_method');
            }
        });
    }
);
