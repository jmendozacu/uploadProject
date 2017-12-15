<?php

namespace Magedelight\Looknbuy\Block\Adminhtml\Look\Edit\Tab;

/**
 * Adminhtml look edit form.
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var string
     */
    const FORM_NAME = 'admin_looknby_form';

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config       $wysiwygConfig
     * @param \Magento\Store\Model\System\Store       $systemStore
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
            \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Init form.
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('look_form');
        $this->setTitle(__('Look Information'));
    }

    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('look_look');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('look_');
        $htmlIdPrefix = $form->getHtmlIdPrefix();
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General Information')]
        );

        if ($model->getLookId()) {
            $fieldset->addField('look_id', 'hidden', ['name' => 'look_id']);
        }

        $fieldset->addField(
            'look_name',
            'text',
            ['name' => 'look_name', 'label' => __('Look Title'), 'title' => __('Look Title'), 'required' => true]
        );

        $fieldset->addField(
            'url_key',
            'text',
            [
                'name' => 'url_key',
                'label' => __('URL Key'),
                'title' => __('URL Key'),
                'class' => 'validate-identifier',
                'required' => false,
                'note' => __('Relative to Web Site Base URL'),

            ]
        );


        $ishome = $fieldset->addField(
            'is_homepage',
            'select',
            [
                'label' => __('Is Homepage'),
                'title' => __('Is Homepage'),
                'name' => 'is_homepage',
                'required' => true,
                'options' => ['0' => __('Inspiration Pages'), '1' => __('Home Page'), '2' => __('Category Pages')],
            ]
        );

        $form = $this->addCategoryFieldset($form, $model->getData(), $fieldset);

        if ($model->getId()) {
            $image = $model->getBaseImage();
            $isImage = 0;
            if (isset($image) && $image != null) {
                $isImage = 1;
            }
        } else {
            $isImage = 0;
        }

        $baseImage = $fieldset->addField(
            'base_image',
            'image',
            [
                'name' => 'base_image',
                'label' => __('Base Image'),
                'title' => __('Base Image'),
                'required' => true,
                'class' => 'required-entry required-file',

            ]
        );

        if ($isImage == 0) {
            $baseImage->setAfterElementHtml('<script type="text/javascript">$("look_base_image").addClassName("required-entry");</script>');
        }

        $fieldset->addField(
            'discount_type',
            'select',
            [
                'label' => __('Discount Type'),
                'title' => __('Discount Type'),
                'name' => 'discount_type',
                'required' => true,
                'options' => ['1' => __('Fixed'), '0' => __('Percentage')],
            ]
        );

        $fieldset->addField(
            'discount_price',
            'text',
            [
                'name' => 'discount_price',
                'label' => __('Discount'),
                'title' => __('Discount'),

            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
                'required' => true,
                'options' => ['1' => __('Enabled'), '0' => __('Disabled')],
            ]
        );
        if (!$model->getId()) {
            $model->setData('status', '1');
        }

        $fieldset->addField(
            'layout',
            'select',
            [
                'label' => __('Layout'),
                'title' => __('Layout'),
                'name' => 'layout',
                'required' => true,
                'options' => ['1' => __('1 Column'), '2' => __('2 Columns')],
            ]
        );

        $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);

        $contentField = $fieldset->addField(
            'description',
            'editor',
            [
                'name' => 'description',
                'style' => 'height:25em;',
                'required' => false,
                'config' => $wysiwygConfig,
            ]
        );


        // write this before  this line $this->setForm($form);
        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Form\Element\Dependence'
            )->addFieldMap(
                $ishome->getHtmlId(), $ishome->getName()
            )->addFieldMap(
                "{$htmlIdPrefix}category_tree_container",
                'category_tree_container'
            )
            ->addFieldDependence(
                'category_tree_container',
                'is_homepage',
                '2'
            )
        );


 // Setting custom renderer for content field to remove label column
        $renderer = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element'
        )->setTemplate(
            'Magento_Cms::page/edit/form/renderer/content.phtml'
        );
        $contentField->setRenderer($renderer);

        $this->_eventManager->dispatch('adminhtml_cms_page_edit_tab_content_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Add category fieldset
     *
     * @param \Magento\Framework\Data\Form $form
     * @param array $formData
     * @return \Magento\Framework\Data\Form
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function addCategoryFieldset($form, $formData , $fieldset)
    {
        $categoryTreeBlock = $this->getLayout()->createBlock(
            \Magento\Catalog\Block\Adminhtml\Category\Checkboxes\Tree::class,
            null,
            ['data' => ['js_form_object' => 'сategoryIds']]
        );

        //$catalogFieldset = $form->addFieldset('category_fieldset', []);
        $fieldset->addField(
            'category_ids',
            'hidden',
            [
                'name' => 'category_ids',
                'data-form-part' => self::FORM_NAME,
                'after_element_js' => $this->getCategoryIdsJs(),
                'value' => isset($formData['category_ids']) ? $formData['category_ids'] : ''
            ]
        );

        if (isset($formData['category_ids'])) {
            $categoryTreeBlock->setCategoryIds(explode(',', $formData['category_ids']));
        }

        $fieldset->addField(
            'category_tree_container',
            'note',
            [
                'label' => __('Category'),
                'title' => __('Category'),
                'text' => $categoryTreeBlock->toHtml()
            ]
        );

        return $form;
    }

    /**
     * Retrive js code for CategoryIds input field
     *
     * @return string
     */
    private function getCategoryIdsJs()
    {
        return <<<HTML
    <script type="text/javascript">
        сategoryIds = {updateElement : {value : "", linkedValue : ""}};
        Object.defineProperty(сategoryIds.updateElement, "value", {
            get: function() {
                return сategoryIds.updateElement.linkedValue
            },
            set: function(v) {
                сategoryIds.updateElement.linkedValue = v;
                jQuery("#look_category_ids").val(v)
            }
        });
    </script>
HTML;
    }


    /**
     * Prepare label for tab.
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Look Information');
    }

    /**
     * Prepare title for tab.
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Look Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
