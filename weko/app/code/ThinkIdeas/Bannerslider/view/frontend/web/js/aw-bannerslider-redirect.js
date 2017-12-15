/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

/**
 * Initialization widget for redirect
 *
 * @method click()
 */
define([
    'jquery'
], function($) {
    "use strict";

    $.widget('mage.awBannersliderRedirect', {
        options: {
            link: '#',
            openUrlInNewWindow: false
        },

        /**
         * Initialize widget
         */
        _create: function () {
            this.element.on('click', $.proxy(this.click, this));
        },

        /**
         * Redirect to url
         */
        click: function(event)
        {
            if (this.options.openUrlInNewWindow) {
                window.open(this.options.link, '_blank');
            } else {
                location.href = this.options.link;
            }
            event.preventDefault();
        }
    });

    return $.mage.awBannersliderRedirect;
});
