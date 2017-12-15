<?php
/**
 * ThinkIdeas
 *
 * @category    ThinkIdeas
 * @package     ThinkIdeas_Faq
 *
 */
namespace ThinkIdeas\Faq\Block\Adminhtml\Category\Edit\Tab;
/**
 * Class Form
 * @package ThinkIdeas\Faq\Block\Adminhtml\Category\Edit\Tab
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{


    /**
     *
     */
    const STATUS_ENABLED = 1;
    /**
     *
     */
    const STATUS_DISABLED = 2;

    /**
     * @var \ThinkIdeas\Faq\Model\ResourceModel\Categoryvalue\CollectionFactory
     */
    protected $_categoryValueCollectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \ThinkIdeas\Faq\Model\ResourceModel\Categoryvalue\CollectionFactory $categoryValueCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \ThinkIdeas\Faq\Model\ResourceModel\Categoryvalue\CollectionFactory $categoryValueCollectionFactory,
        array $data = array()
    )
    {
        $this->_categoryValueCollectionFactory = $categoryValueCollectionFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }


    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout() {
        $this->getLayout()->getBlock('page.title')->setPageTitle($this->getPageTitle());

        \Magento\Framework\Data\Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock(
                'ThinkIdeas\Faq\Block\Adminhtml\Form\Category\Renderer\Fieldset\Element',
                $this->getNameInLayout() . '_fieldset_element'
            )
        );
    }


    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {

        $model = $this->_coreRegistry->registry('current_category');
        $isElementDisabled = false;
        $storeViewId = $this->getRequest()->getParam('store');

        $attributesInStore = $this->_categoryValueCollectionFactory
            ->create()
            ->addFieldToFilter('category_id', $model->getCategoryId())
            ->addFieldToFilter('store_id', $storeViewId)
            ->getColumnValues('attribute_code');

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('Category Information')));

        if ($model->getCategoryId()) {
            $fieldset->addField('category_id', 'hidden', array('name' => 'category_id'));
        }

        $elements =array();

        $elements['name']=$fieldset->addField('name', 'text', array(
            'label'     => __('Name'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'name',
            'disabled' => false
        ));
        $elements['ordering']=$fieldset->addField(
            'ordering', 'text', array(
                'name' => 'ordering',
                'required' => true,
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
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Category Information');
    }


    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Category Information');
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
     * @return mixed
     */
    public function getCategory() {
        return $this->_coreRegistry->registry('current_category');
    }


    /**
     * @return \Magento\Framework\Phrase
     */
    public function getPageTitle() {
        return $this->getCategory()->getCategoryId() ? __("Edit '%1'",
            $this->escapeHtml($this->getCategory()->getName())) : __('New Category');
    }

}
