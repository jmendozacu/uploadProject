/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        'ko'
    ],
    function($, ko) {
        var steps = ko.observableArray();
        return {
            steps: steps,
            stepCodes: [],
            validCodes: [],

            handleHash: function () {

                $("#back-step-cart").css("display", "block");
                $("#back-step-payment").css("display", "none");
                $("#back-step-shipping").css("display", "none");

                $( "#back-step-payment a" ).click(function() {
                    $(".opc-progress-bar li:nth-child(3) span").trigger("click");
                });

                $( "#back-step-shipping a" ).click(function() {
                    $(".opc-progress-bar li:nth-child(2) span").trigger("click");
                });

                $( "#back-step-cart a" ).click(function() {
                    $(".opc-progress-bar li:nth-child(1) span").trigger("click");
                });

                var hashString = window.location.hash.replace('#', '');

                if (hashString == '') {
                    return false;
                }

                $("#review-static-block").css("display", "block");
                $("#review-cart-items").css("display", "block");
                $("#review-cart-items").appendTo("#review-cart");
                $("#review-static-block").appendTo(".review-texts");

                if (window.location.hash == '#shipping') {
                    $(".opc-wrapper").removeClass("payment-step");
                    $(".opc-wrapper").removeClass("review-step");
                    $(".opc-wrapper").addClass("shipping-step");

                    $("#back-step-cart").css("display", "block");
                    $("#back-step-payment").css("display", "none");
                    $("#back-step-shipping").css("display", "none");
                } else if (window.location.hash == '#payment') {
                    $(".opc-wrapper").removeClass("shipping-step");
                    $(".opc-wrapper").removeClass("review-step");
                    $(".opc-wrapper").addClass("payment-step");

                    $("#back-step-shipping").css("display", "block");
                    $("#back-step-payment").css("display", "none");
                    $("#back-step-cart").css("display", "none");

                } else if (window.location.hash == '#order_summary'){
                    $(".opc-wrapper").removeClass("shipping-step");
                    $(".opc-wrapper").removeClass("payment-step");
                    $(".opc-wrapper").addClass("review-step");

                    $("#back-step-payment").css("display", "block");
                    $("#back-step-cart").css("display", "none");
                    $("#back-step-shipping").css("display", "none");
                } else {
                    $("#back-step-shipping").css("display", "block");
                    $("#back-step-payment").css("display", "none");
                    $("#back-step-cart").css("display", "none");

                    $(".opc-wrapper").addClass("shipping-step");
                }

                if (-1 == $.inArray(hashString, this.validCodes)) {
                    window.location.href = window.checkoutConfig.pageNotFoundUrl;
                    return false;
                }

                var isRequestedStepVisible = steps.sort(this.sortItems).some(function(element) {
                    return (element.code == hashString || element.alias == hashString) && element.isVisible();
                });



                //if requested step is visible, then we don't need to load step data from server
                if (isRequestedStepVisible) {
                    return false;
                }

                steps.sort(this.sortItems).forEach(function(element) {
                    if (element.code == hashString || element.alias == hashString) {
                        element.navigate();
                    } else {
                        element.isVisible(false);
                    }

                });
                return false;
            },

            registerStep: function(code, alias, title, isVisible, navigate, sortOrder) {
                if (-1 != $.inArray(code, this.validCodes)) {
                    throw new DOMException('Step code [' + code + '] already registered in step navigator');
                }
                if (alias != null) {
                    if (-1 != $.inArray(alias, this.validCodes)) {
                        throw new DOMException('Step code [' + alias + '] already registered in step navigator');
                    }
                    this.validCodes.push(alias);
                }
                this.validCodes.push(code);
                steps.push({
                    code: code,
                    alias: alias != null ? alias : code,
                    title : title,
                    isVisible: isVisible,
                    navigate: navigate,
                    sortOrder: sortOrder
                });
                this.stepCodes.push(code);
                var hash = window.location.hash.replace('#', '');
                if (hash != '' && hash != code) {
                    //Force hiding of not active step
                    isVisible(false);
                }
            },

            sortItems: function(itemOne, itemTwo) {
                return itemOne.sortOrder > itemTwo.sortOrder ? 1 : -1
            },

            getActiveItemIndex: function() {
                var activeIndex = 0;
                steps.sort(this.sortItems).forEach(function(element, index) {
                    if (element.isVisible()) {
                        activeIndex = index;
                    }
                });
                return activeIndex;
            },

            isProcessed: function(code) {
                var activeItemIndex = this.getActiveItemIndex();
                var sortedItems = steps.sort(this.sortItems);
                var requestedItemIndex = -1;
                sortedItems.forEach(function(element, index) {
                    if (element.code == code) {
                        requestedItemIndex = index;
                    }
                });
                return activeItemIndex > requestedItemIndex;
            },

            navigateTo: function(code, scrollToElementId) {
                var sortedItems = steps.sort(this.sortItems);
                var bodyElem = $.browser.safari || $.browser.chrome ? $("body") : $("html");
                scrollToElementId = scrollToElementId || null;

                if (!this.isProcessed(code)) {
                    return;
                }
                sortedItems.forEach(function(element) {
                    if (element.code == code) {
                        element.isVisible(true);
                        bodyElem.animate({scrollTop: $('#' + code).offset().top}, 0, function () {
                            window.location = window.checkoutConfig.checkoutUrl + "#" + code;
                        });
                        if (scrollToElementId && $('#' + scrollToElementId).length) {
                            bodyElem.animate({scrollTop: $('#' + scrollToElementId).offset().top}, 0);
                        }
                    } else {
                        element.isVisible(false);
                    }

                });
            },

            next: function() {
                var activeIndex = 0;
                steps.sort(this.sortItems).forEach(function(element, index) {
                    if (element.isVisible()) {
                        element.isVisible(false);
                        activeIndex = index;
                    }
                });
                if (steps().length > activeIndex + 1) {
                    var code = steps()[activeIndex + 1].code;
                    steps()[activeIndex + 1].isVisible(true);
                    window.location = window.checkoutConfig.checkoutUrl + "#" + code;
                    document.body.scrollTop = document.documentElement.scrollTop = 0;
                }
            }
        };
    }
);
