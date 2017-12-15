<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Model\Source;

/**
 * Class AnimationEffect
 * @package ThinkIdeas\Bannerslider\Model\Source
 */
class AnimationEffect implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Animation effect values
     */
    const SLIDE = 0;
    const FADE_OUT_IN = 1;
    const SCALE = 2;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::SLIDE,  'label' => __('Slide')],
            ['value' => self::FADE_OUT_IN,  'label' => __('Fade Out / In')],
            ['value' => self::SCALE,  'label' => __('Scale')],
        ];
    }
}
