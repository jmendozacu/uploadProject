<?php
/**
 * ThinkIdeas
 *
 * @category    ThinkIdeas
 * @package     ThinkIdeas_Faq
 *
 */
namespace ThinkIdeas\Faq\Block\Adminhtml\Category;
/**
 * Class Grid
 * @package ThinkIdeas\Faq\Block\Adminhtml\Category
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \ThinkIdeas\Faq\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_categoryCollectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \ThinkIdeas\Faq\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \ThinkIdeas\Faq\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        array $data = array()
    )
    {
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('categoryGrid');
        $this->setDefaultSort('category_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }


    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $storeViewId = $this->getRequest()->getParam('store');
        $collection = $this->_categoryCollectionFactory->create()
            ->setStoreViewId($storeViewId);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    protected function _getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        return $this->_storeManager->getStore($storeId);
    }


    /**
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'category_id',
            array(
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'category_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            )
        );
        $this->addColumn(
            'name',
            array(
                'header' => __('Name'),
                'type' => 'text',
                'index' => 'name',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            )
        );
        $this->addColumn(
            'ordering',
            array(
                'header' => __('Sort Order'),
                'type' => 'number',
                'index' => 'ordering',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            )
        );

        $this->addColumn(
            'status',
            array(
                'header' => __('Status'),
                'index' => 'status',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'type' => 'options',
                'options' => array(
                    1 => 'Enabled',
                    2 => 'Disabled',
                ),
            )
        );
        $this->addColumn(
            'edit',
            [
                'header' => __('Action'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => ['base' => '*/*/edit'],
                        'field' => 'id',
                    ],
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action',
                'is_system' => true
            ]
        );

        $this->addExportType('*/*/exportCsv', __('CSV'));
        $this->addExportType('*/*/exportExcel', __('Excel XML'));
        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('faqadmin/*/grid', array('_current' => true));
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            'faqadmin/*/edit',
            array('store' => $this->getRequest()->getParam('store'), 'id' => $row->getId())
        );
    }


    /**
     * @return $this
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField('category_id');
        $this->getMassactionBlock()->setFormFieldName('category');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('faqadmin/*/massDelete'),
                'confirm' => __('Are you sure?'),
            ]
        );


        $this->getMassactionBlock()->addItem(
            'status',
            [
                'label' => __('Change status'),
                'url' => $this->getUrl('faqadmin/*/massStatus', ['_current' => true]),
                'additional' => [
                    'visibility' => [
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => __('Status'),
                        'values' => array(
                            1 => 'Enabled',
                            2 => 'Disabled',
                        ),
                    ],
                ],
            ]
        );
        return $this;
    }
}
