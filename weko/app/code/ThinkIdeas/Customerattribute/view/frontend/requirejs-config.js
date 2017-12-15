var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/set-shipping-information': {
                'ThinkIdeas_Customerattribute/js/action/set-shipping-information-mixin': true
            }
        }
    },
     map: {
        '*': {
           "Magento_Checkout/js/model/shipping-save-processor/default": 'ThinkIdeas_Customerattribute/js/model/shipping-save-processor/default'
        }
    }
};