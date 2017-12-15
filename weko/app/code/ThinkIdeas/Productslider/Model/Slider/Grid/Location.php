<?php
/**
 * Copyright © 2016 thinkIdeas (http://www.thinkIdeas.co/) All rights reserved.
 */

namespace ThinkIdeas\Productslider\Model\Slider\Grid;

/**
 * To option slider locations array
 * @return array
 */
class Location implements \Magento\Framework\Option\ArrayInterface{

    public function toOptionArray(){
        return \ThinkIdeas\Productslider\Model\Productslider::getSliderGridLocations();
    }
}