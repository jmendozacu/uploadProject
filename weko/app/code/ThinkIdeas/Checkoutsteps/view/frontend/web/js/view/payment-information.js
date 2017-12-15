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
                template: 'ThinkIdeas_Checkoutsteps/payment-information'
            },

            isVisible: function() {
                return !quote.isVirtual() && stepNavigator.isProcessed('payment');
            },

            getPaymentMethodTitle: function() {
                var paymentMethod = quote.paymentMethod();
                return paymentMethod ? paymentMethod.method : '';
            },

            back: function() {
                sidebarModel.hide();
                stepNavigator.navigateTo('payment');
            },

            backToPaymentMethod: function() {
                sidebarModel.hide();
                stepNavigator.navigateTo('payment', 'opc-payment_method');
            }
        });
    }
);
