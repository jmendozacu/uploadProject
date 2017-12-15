/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'Magento_Ui/js/grid/columns/column'
], function (Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'ThinkIdeas_Bannerslider/ui/grid/cells/name'
        },
        getName: function(row) {
            return row[this.index];
        },
        getUrl: function(row) {
            return row[this.index + '_url'];
        }
    });
});
