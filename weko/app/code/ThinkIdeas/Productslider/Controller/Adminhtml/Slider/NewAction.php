<?php
/**
 * Copyright © 2016 thinkIdeas (http://www.thinkIdeas.co/) All rights reserved.
 */

namespace ThinkIdeas\Productslider\Controller\Adminhtml\Slider;

class NewAction extends \ThinkIdeas\Productslider\Controller\Adminhtml\Slider
{
    /**
     * Create new slider action
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute(){
        //Forward to the edit action
        $resultForward = $this->_resultForwardFactory->create();
        $resultForward->forward('edit');
        return $resultForward;
    }
}