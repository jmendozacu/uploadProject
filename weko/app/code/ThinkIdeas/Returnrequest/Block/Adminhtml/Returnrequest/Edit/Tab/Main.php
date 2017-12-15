<?php

namespace ThinkIdeas\Returnrequest\Block\Adminhtml\Returnrequest\Edit\Tab;

/**
 * Returnrequest edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \ThinkIdeas\Returnrequest\Model\Status
     */
    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \ThinkIdeas\Returnrequest\Model\Status $status,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_status = $status;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /* @var $model \ThinkIdeas\Returnrequest\Model\BlogPosts */
        $model = $this->_coreRegistry->registry('returnrequest');

        $isElementDisabled = false;

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Item Information')]);

        if ($model->getId()) {
            $fieldset->addField('rr_id', 'hidden', ['name' => 'rr_id']);
        }

		
        $fieldset->addField(
            'order_no',
            'text',
            [
                'name' => 'order_no',
                'label' => __('Order No'),
                'title' => __('Order No'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'cust_name',
            'text',
            [
                'name' => 'cust_name',
                'label' => __('Customer Name'),
                'title' => __('Customer Name'),
				
                'disabled' => $isElementDisabled
            ]
        );
					

        $dateFormat = $this->_localeDate->getDateFormat(
            \IntlDateFormatter::MEDIUM
        );
        $timeFormat = $this->_localeDate->getTimeFormat(
            \IntlDateFormatter::MEDIUM
        );

        $fieldset->addField(
            'rr_date',
            'date',
            [
                'name' => 'rr_date',
                'label' => __('Date'),
                'title' => __('Date'),
                    'date_format' => $dateFormat,
                    //'time_format' => $timeFormat,
				
                'disabled' => $isElementDisabled
            ]
        );
						
						
						
        $fieldset->addField(
            'item_no',
            'text',
            [
                'name' => 'item_no',
                'label' => __('Item No'),
                'title' => __('Item No'),
				
                'disabled' => $isElementDisabled
            ]
        );
		
         $fieldset->addField(
            'customer_email',
            'text',
            [
                'name' => 'customer_email',
                'label' => __('Customer Email'),
                'title' => __('Customer Email'),
				
                'disabled' => $isElementDisabled
            ]
        );
 $fieldset->addField(
            'reason',
            'textarea',
            [
                'name'      => 'reason',
                'label'     => __('Reason'),
                'required'  => false,
                'style'     => 'height: 15em; width: 30em;'
            ]
        );

        if (!$model->getId()) {
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
        }

        $form->setValues($model->getData());
        $this->setForm($form);
		
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Item Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Item Information');
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

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
    
    public function getTargetOptionArray(){
    	return array(
    				'_self' => "Self",
					'_blank' => "New Page",
    				);
    }
}
