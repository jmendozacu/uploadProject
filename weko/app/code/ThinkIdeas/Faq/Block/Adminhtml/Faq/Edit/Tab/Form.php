<?php
/**
 * ThinkIdeas
 *
 * @category    ThinkIdeas
 * @package     ThinkIdeas_Faq
 *
 */
namespace ThinkIdeas\Faq\Block\Adminhtml\Faq\Edit\Tab;
/**
 * Class Form
 * @package ThinkIdeas\Faq\Block\Adminhtml\Faq\Edit\Tab
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;
    /**
     * @var \ThinkIdeas\Faq\Model\FaqvalueFactory
     */
    protected $_valueFactory;
    /**
     * @var \ThinkIdeas\Faq\Model\ResourceModel\Faqvalue\CollectionFactory
     */
    protected $_faqValueFactory;
    /**
     * @var \ThinkIdeas\Faq\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_categoryCollectionFactory;
    /**
     * @var \ThinkIdeas\Faq\Helper\Data
     */
    protected $_faqHelper;
    /**
     *
     */
    const STATUS_ENABLED = 1;
    /**
     *
     */
    const STATUS_DISABLED = 2;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \ThinkIdeas\Faq\Model\FaqvalueFactory $valueFactory
     * @param \ThinkIdeas\Faq\Model\ResourceModel\Faqvalue\CollectionFactory $faqValueFactory
     * @param \ThinkIdeas\Faq\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \ThinkIdeas\Faq\Helper\Data $faqHelper
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \ThinkIdeas\Faq\Model\FaqvalueFactory $valueFactory,
        \ThinkIdeas\Faq\Model\ResourceModel\Faqvalue\CollectionFactory $faqValueFactory,
        \ThinkIdeas\Faq\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \ThinkIdeas\Faq\Helper\Data $faqHelper,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = array()
    )
    {
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_valueFactory = $valueFactory;
        $this->_faqValueFactory = $faqValueFactory;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_faqHelper = $faqHelper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout() {
        $this->getLayout()->getBlock('page.title')->setPageTitle($this->getPageTitle());

        \Magento\Framework\Data\Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock(
                'ThinkIdeas\Faq\Block\Adminhtml\Form\Faq\Renderer\Fieldset\Element',
                $this->getNameInLayout() . '_fieldset_element'
            )
        );
    }

    /**
     * @param $category_id
     * @param null $store_id
     * @return array
     */
    public function getCategoryOptions($category_id, $store_id = null)
    {
        $options = array();
        $collection = $this->_categoryCollectionFactory->create()
            ->setStoreViewId($store_id)
        ;
        if(!$category_id)
            $collection->addFieldToFilter("status",1);

        foreach($collection as $category)
        {
            $options[$category->getCategoryId()] = $category->getName();
        }

        return $options;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {

        $model = $this->_coreRegistry->registry('current_faq');
        $store_id = $this->getRequest()->getParam('store');
        $isElementDisabled = false;
        $storeViewId = $this->getRequest()->getParam('store');

        $attributesInStore = $this->_faqValueFactory
            ->create()
            ->addFieldToFilter('faq_id', $model->getFaqId())
            ->addFieldToFilter('store_id', $storeViewId)
            ->getColumnValues('attribute_code');

        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('FAQ Information')));

        if ($model->getId()) {
            $fieldset->addField('faq_id', 'hidden', array('name' => 'faq_id'));
        }
        $boxTags = $this->_faqHelper->getHtmlTags($this->getRequest()->getParam('store'));

        if ($model) {
            $categoryId = $model->getData('category_id');
        } else {
            $categoryId = 0;
        }


        $config = array(
            'enabled' => true,
            'hidden' => true,
            'use_container' => true,
            'add_variables' => false,
            'add_widgets' => false
        );
        $wysiwygConfig = $this->_wysiwygConfig->getConfig($config);

        $elements = array();
        $elements['title']=$fieldset->addField(
            'title', 'text', array(
                'name' => 'title',
                'required' => true,
                'label' => __('Title'),
                'title' => __('Title'),
                'disabled' => $isElementDisabled,
            )
        );

        $elements['description']=$fieldset->addField(
            'description', 'editor', array(
                'name' => 'description',
                'required' => false,
                'label' => __('Description'),
                'title' => __('Description'),
                'style' => 'width:500px; height:300px;',
                'wysiwyg' => true,
                'config' => $wysiwygConfig,
                'disabled' => $isElementDisabled,
            )
        );
        
        $elements['category_id']=$fieldset->addField(
            'category_id', 'select', array(
                'name' => 'category_id',
                'required' => true,
                'label' => __('Category'),
                'title' => __('Category'),
                'disabled' => $isElementDisabled,
                'options' => $this->getCategoryOptions($categoryId,$store_id)
            )
        );
        
        
        $elements['ordering']=$fieldset->addField(
            'ordering', 'text', array(
                'name' => 'ordering',
                'required' => false,
                'label' => __('Sort Order'),
                'title' => __('Sort Order'),
                'disabled' => $isElementDisabled,
            )
        );
        $elements['status']=$fieldset->addField(
            'status', 'select', array(
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
                'required' => false,
                'options' => array(
                    1 => 'Enabled',
                    2 => 'Disabled',
                ),
                'disabled' => $isElementDisabled
            )
        );
        foreach ($attributesInStore as $attribute) {
            $elements[$attribute]->setStoreViewId($storeViewId);
        }
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * @return mixed
     */
    public function getFaq() {
        return $this->_coreRegistry->registry('current_faq');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getPageTitle() {
        return $this->getFaq()->getFaqId() ? __("Edit '%1'",
            $this->escapeHtml($this->getFaq()->getTitle())) : __('New FAQ');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('FAQ Information');
    }


    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('FAQ Information');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @param $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }


    /**
     * @param string $html
     * @return string
     */
    protected function _afterToHtml($html)
    {
        $script = "
            <script>
                require([
                    'jquery'
                ], function($){
                        var page_tag = $('#page_tag');
                        var TagParent = page_tag.parent();
                        page_tag.css('border-width','1px');
                        if (TagParent.find('.addafter')&& TagParent) {
                            TagParent.find('.addafter').removeClass('addafter');
                        }
                        page_tag.focus(function() {
                        $('#box-tags').show();

                    });
                    $('#close-box').click(function(){
                        $('#box-tags').hide();
                    });
                    $('#box-tags').find('a').each(function(){
                       if ($(this).attr('id')!='close-box') {
                            $(this).click(function(){
                                value = $(this).html();
                                var tag = $('#page_tag');
                                var old = tag.val();
                                if (old!='') {
                                    tag.val(old+','+value);
                                } else {
                                    tag.val(value);
                                }
                            });
                        }
                    });
                });
            </script>";
        $html = $html . $script;
        return parent::_afterToHtml($html);
    }


}
