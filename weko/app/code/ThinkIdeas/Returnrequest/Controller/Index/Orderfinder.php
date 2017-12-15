<?php

namespace ThinkIdeas\Returnrequest\Controller\Index;

use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;

class Orderfinder extends \Magento\Framework\App\Action\Action
{

    protected $formKeyValidator;
    protected $storeManager;
    protected $_coreRegistry;
    protected $customerRepository;
    protected $subscriberFactory;

    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Registry $coreRegistry, CustomerRepository $customerRepository
    )
    {
        $this->storeManager = $storeManager;
        $this->formKeyValidator = $formKeyValidator;
        $this->customerRepository = $customerRepository;
        $this->_coreRegistry = $coreRegistry;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    public function execute()
    {
        $this->_view->loadLayout();
        $post = $this->getRequest()->getPostValue();
        $we = $this->getRequest()->getParams();
        $this->_coreRegistry->register('rrq_order_id',$this->getRequest()->getPostValue("orderno"));
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }

}
