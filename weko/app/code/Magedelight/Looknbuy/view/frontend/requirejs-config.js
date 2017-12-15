/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

 var config = {
 	map: {
 		'*': {
            custompriceBox:  'Magedelight_Looknbuy/js/custompricebox',
            priceBundleCustom:  'Magedelight_Looknbuy/js/price-bundle',
            wookmark:  'Magedelight_Looknbuy/js/wookmark',
            slicklook: 'Magedelight_Looknbuy/js/slick.min'


        }
    },

        paths: {
            easing: 'Magedelight_Looknbuy/js/easing',
            easypin: 'Magedelight_Looknbuy/js/easypin',
        },
        shim: {
            'easing': {
                deps: ['jquery']
            },
            'easypin': {
                deps: ['jquery']
            }
        }

};