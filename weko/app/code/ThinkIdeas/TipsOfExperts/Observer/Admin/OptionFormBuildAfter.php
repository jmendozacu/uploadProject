<?php

namespace ThinkIdeas\TipsOfExperts\Observer\Admin;

use Magento\Catalog\Model\Category\Attribute\Source\Page;
use Magento\Framework\Data\Form;
use Magento\Framework\Event\ObserverInterface;

class OptionFormBuildAfter implements ObserverInterface
{
    /** @var Page */
    protected $page;

    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var Form $form */
        $form = $observer->getData('form');

        $this->addTipsOfExpertsSection($form);
    }

    protected function addTipsOfExpertsSection(\Magento\Framework\Data\Form $form)
    {
        $fieldset = $form->addFieldset('tips_of_experts_section', ['legend' => __('Tips of Experts'), 'class'=> 'form-inline']);

        $fieldset->addField('block_layout', 'hidden', [
            'name'  => 'block_layout',
            'label' => __('Block Layout')
        ]);

        $fieldset->addType(
            'brand_field',
            '\ThinkIdeas\TipsOfExperts\Block\Adminhtml\Brand\Field'
        );
        
        $fieldset->addField('brand_field', 'brand_field', [
            'name'  => 'brand_field'
        ]);
    }
}
