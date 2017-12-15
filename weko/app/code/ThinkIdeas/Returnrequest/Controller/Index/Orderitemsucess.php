<?php

namespace ThinkIdeas\Returnrequest\Controller\Index;

use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;

class Orderitemsucess extends \Magento\Framework\App\Action\Action
{

    protected $formKeyValidator;
    protected $storeManager;
    protected $_coreRegistry;
    protected $customerRepository;
    protected $subscriberFactory;
    protected $_returnreqobje;
    protected $_returnrequestFactory;

    public function __construct(
    \Magento\Framework\App\Action\Context $context,
    \Magento\Customer\Model\Session $customerSession,
    \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
    \Magento\Store\Model\StoreManagerInterface $storeManager,
    \Magento\Framework\Registry $coreRegistry,
    \ThinkIdeas\Returnrequest\Model\ReturnrequestFactory $ReturnrequestFactory,
    CustomerRepository $customerRepository,
    \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
    \ThinkIdeas\Returnrequest\Helper\Data $helper
    )
    {
        $this->storeManager = $storeManager;
        $this->formKeyValidator = $formKeyValidator;
        $this->customerRepository = $customerRepository;
        $this->_coreRegistry = $coreRegistry;
        $this->customerSession = $customerSession;
        $this->_returnrequestFactory = $ReturnrequestFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->helper = $helper;
        parent::__construct($context);
    }

    public function execute()
    {
        $this->_view->loadLayout();
        $we = $this->getRequest()->getParams();
        $collection = $this->_returnrequestFactory->create()->getCollection();
        $collection->addFieldToFilter('customer_email', array('like' => $this->customerSession->getCustomer()->getData("email")))
                ->addFieldToFilter('order_no', array('like' => $this->getRequest()->getPostValue("orderidhiddne")))
                ->addFieldToFilter('item_no', array('like' => $this->getRequest()->getPostValue("itemno")));

        $errors = array_filter($collection->getData());

        if (empty($errors)) {
            //array is empty go ahead and save the data    
            if ($this->customerSession->isLoggedIn()) {
                $model = $this->_objectManager->create('ThinkIdeas\Returnrequest\Model\Returnrequest');
                $customerName = $this->customerSession->getCustomer()->getData("firstname") . " " . $this->customerSession->getCustomer()->getData("lastname");
                $model->setData('order_no', $this->getRequest()->getPostValue("orderidhiddne"));
                $model->setData('cust_name', $customerName);
                $model->setData('date', date('Y-m-d H:i:s'));
                $model->setData('item_no', $this->getRequest()->getPostValue("itemno"));
                $model->setData('customer_email', $this->customerSession->getCustomer()->getData("email"));
                $model->setData('reason', $this->getRequest()->getPostValue("reason"));

                try {
                    $model->save();

                    /* custom logic for send transaction mail by rakesh */
                    $adminEmail = $this->getStoreEmail();
                    $fullName = $customerName;
                    $emailId = $this->customerSession->getCustomer()->getData("email");
                    $orderInfo = array();
                    $orderInfo['order_id'] = $this->getRequest()->getPostValue("orderidhiddne"); 
                    $orderInfo['itemno'] = $this->getRequest()->getPostValue("itemno"); 
                    $orderInfo['reason'] = $this->getRequest()->getPostValue("reason");
                    $orderInfo['name'] = $fullName;
                    $orderInfo['email'] = $emailId;
                    
                    $postObject = new \Magento\Framework\DataObject();
                    $postObject->setData($orderInfo);
                    
                    $templateData = ['order' => $postObject];
                    $multipleEmail = array($emailId,$adminEmail);
                    $storeId = $this->storeManager->getStore()->getId();
                    try { 
                        $this->helper->sendEmailTemplate(
                                $fullName,
                                $multipleEmail,
                                $this->helper->getReturnRequestTemplateId(),
                                $this->helper->getSender(\ThinkIdeas\Returnrequest\Helper\Data::TYPE_ADMIN),
                                $templateData,
                                $storeId
                        );
                    } catch(\Exception $e) {  } 
                    /* custom logic end here */
                    $this->_coreRegistry->register('model_saved', $model->getData());
                    $this->messageManager->addSuccess(__('Wir haben deine Retourenanfrage erhalten.'));
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\RuntimeException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('Something went wrong while saving the Returnrequest.'));
                }
            } 
        
        } else {
            $this->messageManager->addError("Du hast bereits eine Retourenanfrage für den Artikel Nr. <b>*".$this->getRequest()->getPostValue("itemno") . "*</b> geschickt");
            $this->_coreRegistry->register('model_saved', array());
        }

        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }
    /*
    * Get Admin email to sent mail.
    */
    public function getStoreEmail()
    {
        return $this->_scopeConfig->getValue(
            'trans_email/ident_general/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

}
