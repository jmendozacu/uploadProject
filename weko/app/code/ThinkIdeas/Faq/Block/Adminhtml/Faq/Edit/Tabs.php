<?php
/**
 * ThinkIdeas
 *
 * @category    ThinkIdeas
 * @package     ThinkIdeas_Faq
 *
 */
namespace ThinkIdeas\Faq\Block\Adminhtml\Faq\Edit;
/**
 * Class Tabs
 * @package ThinkIdeas\Faq\Block\Adminhtml\Faq\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('faq_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('FAQ Information'));
    }

    /**
     * @return $this
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'faq_faq_form',
            [
                'label' => __('General'),
                'title' => __('General'),
                'content' => $this->getLayout()->createBlock('ThinkIdeas\Faq\Block\Adminhtml\Faq\Edit\Tab\Form')->toHtml(),
                'active' => true
            ]
        );
        $this->addTab(
            'faq_faq_meta',
            [
                'label' => __('Meta Information'),
                'title' => __('Meta Information'),
                'content' => $this->getLayout()->createBlock('ThinkIdeas\Faq\Block\Adminhtml\Faq\Edit\Tab\Meta')->toHtml(),
                'active' => false
            ]
        );

        return parent::_beforeToHtml();
    }

}
