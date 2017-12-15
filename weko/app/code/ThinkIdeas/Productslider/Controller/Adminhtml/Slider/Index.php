<?php
/**
 * Copyright © 2016 thinkIdeas (http://www.thinkIdeas.co/) All rights reserved.
 */

namespace ThinkIdeas\Productslider\Controller\Adminhtml\Slider;

class Index extends \ThinkIdeas\Productslider\Controller\Adminhtml\Slider {

    /**
     * Slider index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute(){
        if ($this->getRequest()->getQuery('ajax')) {
            $resultForward = $this->_resultForwardFactory->create();
            $resultForward->forward('grid');
            return $resultForward;
        }
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('ThinkIdeas_Core::admin');

        $resultPage->addBreadcrumb(__('Sliders'), __('Sliders'));
        $resultPage->addBreadcrumb(__('Manage Sliders'), __('Manage Sliders'));

        return $resultPage;
    }
}