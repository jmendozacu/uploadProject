<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Model\Source;

/**
 * Class ImageType
 * @package ThinkIdeas\Bannerslider\Model\Source
 */
class ImageType implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Image type values
     */
    const TYPE_FILE = 1;
    const TYPE_URL = 2;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::TYPE_FILE,  'label' => __('File')],
            ['value' => self::TYPE_URL,  'label' => __('URL')],
        ];
    }
}
