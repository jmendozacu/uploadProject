<?php
/**
 * Copyright © 2016 thinkIdeas (http://www.thinkIdeas.co/) All rights reserved.
 */

namespace ThinkIdeas\Productslider\Model\ResourceModel\Productslider;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    /**
     * Initialize resources
     * @return void
     */
    protected function _construct(){
        $this->_init('ThinkIdeas\Productslider\Model\Productslider','ThinkIdeas\Productslider\Model\ResourceModel\Productslider');
    }

}