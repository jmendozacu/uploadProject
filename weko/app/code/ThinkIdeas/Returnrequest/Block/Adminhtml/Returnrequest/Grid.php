<?php
namespace ThinkIdeas\Returnrequest\Block\Adminhtml\Returnrequest;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \ThinkIdeas\Returnrequest\Model\returnrequestFactory
     */
    protected $_returnrequestFactory;

    /**
     * @var \ThinkIdeas\Returnrequest\Model\Status
     */
    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \ThinkIdeas\Returnrequest\Model\returnrequestFactory $returnrequestFactory
     * @param \ThinkIdeas\Returnrequest\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \ThinkIdeas\Returnrequest\Model\ReturnrequestFactory $ReturnrequestFactory,
        \ThinkIdeas\Returnrequest\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_returnrequestFactory = $ReturnrequestFactory;
        $this->_status = $status;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('postGrid');
        $this->setDefaultSort('rr_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
        $this->setVarNameFilter('post_filter');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_returnrequestFactory->create()->getCollection();
        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'rr_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'rr_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );


		
				$this->addColumn(
					'order_no',
					[
						'header' => __('Order No'),
						'index' => 'order_no',
					]
				);
				
				$this->addColumn(
					'cust_name',
					[
						'header' => __('Customer Name'),
						'index' => 'cust_name',
					]
				);
				
				$this->addColumn(
					'rr_date',
					[
						'header' => __('Date'),
						'index' => 'rr_date',
						'type'      => 'datetime',
					]
				);
					
					
				$this->addColumn(
					'item_no',
					[
						'header' => __('Item No'),
						'index' => 'item_no',
					]
				);
				


		
        //$this->addColumn(
            //'edit',
            //[
                //'header' => __('Edit'),
                //'type' => 'action',
                //'getter' => 'getId',
                //'actions' => [
                    //[
                        //'caption' => __('Edit'),
                        //'url' => [
                            //'base' => '*/*/edit'
                        //],
                        //'field' => 'rr_id'
                    //]
                //],
                //'filter' => false,
                //'sortable' => false,
                //'index' => 'stores',
                //'header_css_class' => 'col-action',
                //'column_css_class' => 'col-action'
            //]
        //);
		

		
		   $this->addExportType($this->getUrl('returnrequest/*/exportCsv', ['_current' => true]),__('CSV'));
		   $this->addExportType($this->getUrl('returnrequest/*/exportExcel', ['_current' => true]),__('Excel XML'));

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

	
    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {

        $this->setMassactionIdField('rr_id');
        //$this->getMassactionBlock()->setTemplate('ThinkIdeas_Returnrequest::returnrequest/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('returnrequest');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('returnrequest/*/massDelete'),
                'confirm' => __('Are you sure?')
            ]
        );

        $statuses = $this->_status->getOptionArray();

        $this->getMassactionBlock()->addItem(
            'status',
            [
                'label' => __('Change status'),
                'url' => $this->getUrl('returnrequest/*/massStatus', ['_current' => true]),
                'additional' => [
                    'visibility' => [
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => __('Status'),
                        'values' => $statuses
                    ]
                ]
            ]
        );


        return $this;
    }
		

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('returnrequest/*/index', ['_current' => true]);
    }

    /**
     * @param \ThinkIdeas\Returnrequest\Model\returnrequest|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
		
        return $this->getUrl(
            'returnrequest/*/edit',
            ['rr_id' => $row->getId()]
        );
		
    }

	

}