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
            bodyTmpl: 'ThinkIdeas_Bannerslider/ui/grid/cells/linkage'
        },
        getLinkageData: function(row) {
            return row[this.index];
        }
    });
});
