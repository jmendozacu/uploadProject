<?php
/**
 * Copyright © 2016 thinkIdeas (http://www.thinkIdeas.co/) All rights reserved.
 */

namespace ThinkIdeas\Productslider\Controller\Adminhtml\Slider;

class Grid extends \ThinkIdeas\Productslider\Controller\Adminhtml\Slider
{
    /**
     * Prevent entire page loading
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
}