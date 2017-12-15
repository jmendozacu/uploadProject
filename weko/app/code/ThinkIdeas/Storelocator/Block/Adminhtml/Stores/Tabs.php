<?php

namespace ThinkIdeas\Storelocator\Block\Adminhtml\Store;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('store_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Store Locator Information'));
    }
}
