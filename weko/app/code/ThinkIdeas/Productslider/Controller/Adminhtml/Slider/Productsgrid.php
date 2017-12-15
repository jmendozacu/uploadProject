<?php
/**
 * Copyright © 2016 thinkIdeas (http://www.thinkIdeas.co/) All rights reserved.
 */

namespace ThinkIdeas\Productslider\Controller\Adminhtml\Slider;

class Productsgrid extends \ThinkIdeas\Productslider\Controller\Adminhtml\Slider
{
    /**
     * Display list of additional products to current slider type
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $sliderId = (int)$this->getRequest()->getParam('id', false);

        $slider = $this->_initSlider($sliderId);
        $this->_coreRegistry->register('product_slider', $slider);

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->_resultRawFactory->create();
        return $resultRaw->setContents(
            $this->_layoutFactory->create()->createBlock(
                'ThinkIdeas\Productslider\Block\Adminhtml\Slider\Edit\Tab\Products',
                'admin.block.slider.tab.products'
            )->toHtml()
        );
    }
}