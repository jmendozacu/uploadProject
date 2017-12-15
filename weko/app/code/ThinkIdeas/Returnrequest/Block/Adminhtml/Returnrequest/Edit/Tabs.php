<?php

namespace ThinkIdeas\Returnrequest\Block\Adminhtml\Returnrequest\Edit;

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
        $this->setId('returnrequest_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Returnrequest Information'));
    }

}
