<?php
/**
 * ThinkIdeas
 *
 * @category    ThinkIdeas
 * @package     ThinkIdeas_Faq
 *
 */
namespace ThinkIdeas\Faq\Block\Adminhtml;
/**
 * Class Faq
 * @package ThinkIdeas\Faq\Block\Adminhtml
 */
class Faq extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_faq';
        $this->_blockGroup = 'ThinkIdeas_Faq';
        $this->_headerText = __('Faq');
        $this->_addButtonLabel = __('Add New FAQ');
        parent::_construct();
    }
}
