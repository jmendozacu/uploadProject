<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Model\Source;

/**
 * Class PageType
 * @package ThinkIdeas\Bannerslider\Model\Source
 */
class PageType implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Page type values
     */
    const HOME_PAGE = 1;
    const PRODUCT_PAGE = 2;
    const CATEGORY_PAGE = 3;
    const CUSTOM_WIDGET = 4;

    /**
     * @return array
     */
    public function getOptionArray()
    {
        $optionArray = [];
        foreach ($this->toOptionArray() as $option) {
            $optionArray[$option['value']] = $option['label'];
        }
        return $optionArray;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::HOME_PAGE,  'label' => __('Home Page')],
            ['value' => self::PRODUCT_PAGE,  'label' => __('Product Pages')],
            ['value' => self::CATEGORY_PAGE,  'label' => __('Catalog Pages')],
            ['value' => self::CUSTOM_WIDGET,  'label' => __('Custom Widget')],
        ];
    }
}
