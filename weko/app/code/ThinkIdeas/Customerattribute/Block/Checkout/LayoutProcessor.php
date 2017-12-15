<?php
/**
 * *
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *  
 */

namespace ThinkIdeas\Customerattribute\Block\Checkout;

/**
 * Class LayoutProcessor
 * @package Magestore\OneStepCheckout\Block\Checkout
 */
class LayoutProcessor implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
{
    /**
     * @param array $jsLayout
     * @return array
     */
    public function process($jsLayout)
    {

        if(isset($jsLayout['components']['checkout']['children']['steps']
            ['children']['shipping-step']['children']['shippingAddress']
            ['children']['shipping-address-fieldset']['children'])) {


            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['dob'] = [
                'component' => 'Magento_Ui/js/form/element/abstract',
                'config' => [
                    'customScope' => 'shippingAddress.custom_attributes',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/date',
                    'options' => ['changeYear' => true,'changeMonth'
                => true,'yearRange'=>'-60:+0'],
                    'id' => 'dob'
                ],
                'dataScope' => 'shippingAddress.custom_attributes.dob',
                'label' => 'Geburtstag',
                'provider' => 'checkoutProvider',
                'visible' => true,
                'validation' => [],
                'sortOrder' => 190,
                'id' => 'dob'
            ];

            $customAttributeCode = 'weko_card_number';
            $wekoCardNumber = [
                'component' => 'Magento_Ui/js/form/element/abstract',
                'config' => [
                    // customScope is used to group elements within a single form (e.g. they can be validated separately)
                    'customScope' => 'shippingAddress.custom_attributes',
                    'customEntry' => null,
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/input',                    
                ],
                'dataScope' => 'shippingAddress.custom_attributes' . '.' . $customAttributeCode,
                'label' => 'Weko Card Number',
                'provider' => 'checkoutProvider',
                'sortOrder' => 200,
                'validation' => [
                   'required-entry' => false
                ],
                'options' => [],
                'filterBy' => null,
                'customEntry' => null,
                'visible' => true,
            ];
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children'][$customAttributeCode] = $wekoCardNumber;

            //$childs = $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children'];
            //$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children'] = $this->processShippingInput($childs);

        } 

        /*if(isset($jsLayout['components']['checkout']['children']['steps']
            ['children']['billing-step']['children']['payment']['children']
            ['payments-list']['children'])) {
            $customAttributeCode = 'weko_card_number';
            $configuration = $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'];
        
            foreach ($configuration as $paymentKey => $paymentConfig) {
                if (isset($paymentConfig['component']) AND $paymentConfig['component'] === 'Magento_Checkout/js/view/billing-address') {
                    
                    $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                    ['payment']['children']['payments-list']['children'][$paymentKey]['children']['form-fields']['children']['weko_card_number'] = [
                        'component' => 'Magento_Ui/js/form/element/abstract',
                        'config' => [
                            'template' => 'ui/form/field',
                            'elementTmpl' => 'ui/form/element/input',
                            'id' => 'weko_card_number',
                        ],
                        'dataScope' => $paymentConfig['dataScopePrefix'] . '.weko_card_number',
                        'label' => __('Weko Card Number'),
                        'provider' => 'checkoutProvider',
                        'visible' => true,
                        'validation' => [
                            'required-entry' => false,
                            'min_text_length' => 0,
                        ],
                        'sortOrder' => 300,
                        'id' => 'weko_card_number'
                    ];
                }
            }

            $customAttributeCode = 'dob';
            $configuration = $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'];

            foreach ($configuration as $paymentKey => $paymentConfig) {

                if (isset($paymentConfig['component']) AND $paymentConfig['component'] === 'Magento_Checkout/js/view/billing-address') {
                    $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                    ['payment']['children']['payments-list']['children'][$paymentKey]['children']['form-fields']['children']['dob'] = [
                        'component' => 'Magento_Ui/js/form/element/abstract',
                        'config' => [
                            'template' => 'ui/form/field',
                            'elementTmpl' => 'ui/form/element/date',
                            'options' => [], // This is required field //
                            'id' => 'dob',
                        ],
                        'dataScope' => $paymentConfig['dataScopePrefix'] . '.dob',
                        'label' => __('Date of Birth'),
                        'provider' => 'checkoutProvider',
                        'visible' => true,
                        'validation' => [],
                        'sortOrder' => 310,
                        'id' => 'dob'
                    ];
                }
            }

        }  */ 

        return $jsLayout;
    } 

    /**
     * @param $payments
     * @return mixed
     */
    public function processBillingInput($payments){
        if(count($payments) > 0){
            echo count($payments).' 2222222';
            foreach($payments as $paymentCode => $paymentComponent){
                
                if (isset($paymentComponent['component']) && $paymentComponent['component'] != "Magento_Checkout/js/view/billing-address") {
                    continue;
                }

                //$paymentComponent['component'] = "Magestore_OneStepCheckout/js/view/billing-address";
                if(isset($paymentComponent['children']['form-fields']['children'])){
                    $childs = $paymentComponent['children']['form-fields']['children'];
                    foreach($childs as $key => $child){
                        echo "<pre>1111111 ";print_r($child);
                        if(isset($child['config']['template']) && $child['config']['template'] == 'ui/group/group' && isset($child['children'])){
                            $childs[$key]['component'] = "Magestore_OneStepCheckout/js/view/form/components/group";
                            if (isset($childs[$key]['children'])) {
                                $children = $childs[$key]['children'];
                                $newChildren = array();
                                foreach ($children as $item) {
                                    $item['config']['elementTmpl'] = "Magestore_OneStepCheckout/form/element/input";
                                    $newChildren[] = $item;
                                }
                                $childs[$key]['children'] = $newChildren;
                            }
                        }
                        if(isset($child['config']) && isset($child['config']['elementTmpl']) && $child['config']['elementTmpl'] == "ui/form/element/input"){
                            $childs[$key]['config']['elementTmpl'] = "Magestore_OneStepCheckout/form/element/input";
                        }
                        if(isset($child['config']) && isset($child['config']['template']) && $child['config']['template'] == "ui/form/field"){
                            $childs[$key]['config']['template'] = "Magestore_OneStepCheckout/form/field";
                        }
                    }
                    $paymentComponent['children']['form-fields']['children'] = $childs;
                    $payments[$paymentCode] = $paymentComponent;
                }
            }
        }
        return $payments;
    }   
}
