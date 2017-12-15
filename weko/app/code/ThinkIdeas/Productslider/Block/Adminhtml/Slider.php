<?php
/**
 * Copyright © 2016 thinkideas (http://www.thinkideas.co/) All rights reserved.
 */

namespace ThinkIdeas\Productslider\Block\Adminhtml;

class Slider extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Modify header & button labels
     *
     * @return void
     */
    protected function _construct(){
        $this->_blockGroup = 'ThinkIdeas_Productslider';
        $this->_controller = 'adminhtml';
        $this->_headerText = 'Slider';
        $this->_addButtonLabel = __('Create New Slider');
        parent::_construct();
    }

}