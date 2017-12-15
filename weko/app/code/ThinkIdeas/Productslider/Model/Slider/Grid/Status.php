<?php
/**
 * Copyright © 2016 thinkIdeas (http://www.thinkIdeas.co/) All rights reserved.
 */

namespace ThinkIdeas\Productslider\Model\Slider\Grid;

class Status implements \Magento\Framework\Option\ArrayInterface {

    /**
     * To option slider statuses array
     * @return array
     */
    public function toOptionArray(){
        return \ThinkIdeas\Productslider\Model\Productslider::getStatusArray();
    }
}