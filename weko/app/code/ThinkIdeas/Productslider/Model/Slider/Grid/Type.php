<?php
/**
 * Copyright © 2016 thinkIdeas (http://www.thinkIdeas.co/) All rights reserved.
 */

namespace ThinkIdeas\Productslider\Model\Slider\Grid;

class Type implements \Magento\Framework\Data\OptionSourceInterface{

    /**
     * To option slider types array
     * @return array
     */
    public function toOptionArray(){
        return \ThinkIdeas\Productslider\Model\Productslider::getSliderTypeArray();
    }
}