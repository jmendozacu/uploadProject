/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        "underscore",
        'ko',
        'uiComponent',
        'Magento_Checkout/js/model/step-navigator',
        "mage/url",
        'jquery/jquery.hashchange'
    ],
    function ($, _, ko, Component, stepNavigator,url) {
        var steps = stepNavigator.steps;
        continueCartUrl = url.build('');
        return Component.extend({
            defaults: {
                template: 'Magento_Checkout/progress-bar',
                visible: true
            },
            steps: steps,

            initialize: function() {
                this._super();
                $(window).hashchange(_.bind(stepNavigator.handleHash, stepNavigator));
                stepNavigator.handleHash();
            },

            sortItems: function(itemOne, itemTwo) {
                return stepNavigator.sortItems(itemOne, itemTwo);
            },

            navigateTo: function(step) {
                stepNavigator.navigateTo(step.code);
            },

            isProcessed: function(item) {
                return stepNavigator.isProcessed(item.code);
            },

            redirectToCart: function(){
                window.location.replace('cart');
            }
        });
    }
);
