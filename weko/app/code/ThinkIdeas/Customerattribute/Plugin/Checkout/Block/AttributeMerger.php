<?php
namespace ThinkIdeas\Customerattribute\Plugin\Checkout\Block;

class AttributeMerger
{
    public function afterMerge(\Magento\Checkout\Block\Checkout\AttributeMerger $subject, $result)
    {

        if (array_key_exists('telephone', $result)) {
            $result['telephone']['placeholder'] = __('Vorwahl+Nummer (zusammen geschrieben)');
            
        }

        return $result;
    }
}