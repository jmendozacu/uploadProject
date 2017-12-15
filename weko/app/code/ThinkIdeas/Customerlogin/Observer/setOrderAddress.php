<?php
namespace ThinkIdeas\Customerlogin\Observer;

use Magento\Framework\Event\ObserverInterface;

class setOrderAddress implements ObserverInterface
{
    protected $_storeFactory;
    protected $orderFactory;

    private $cookieMetadataManager;
    private $cookieMetadataFactory;

    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,        
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
        $this->_resource = $resource;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Set Email based on postcode location
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {       
        $order = $observer->getEvent()->getOrder(); 
        $customerId =  $this->customerSession->getId();
        $customerEmail =  $this->customerSession->getCustomer()->getEmail();
        $customerInfo = $this->customerSession->getCustomer();
        $firstname = $customerInfo->getFirstname();
        $lastname = $customerInfo->getLastname();

        $criteria = $this->searchCriteriaBuilder        
                      ->addFilter('customer_email',$customerEmail)
                      ->create();
        $orderResult = $this->orderRepository->getList($criteria);
        $customerOrderCount = $orderResult->getSize();
       
        if($customerOrderCount){
            $orderId = $this->checkoutSession->getLastOrderId();
            $connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
            $tblSalesOrder = $connection->getTableName('sales_order');
            /* hard code for set customer info inside sales_order table */
            $connection->query("UPDATE `$tblSalesOrder` set customer_id=$customerId,customer_group_id=1, customer_is_guest=0, customer_note_notify=0, customer_firstname = '$firstname',customer_lastname='$lastname' where entity_id=".$orderId);

            /*for set customer billing and shiping as last entry of order placed.*/
            /* customer_address_entity */
            $customerAddressTable = $connection->getTableName('customer_address_entity');
            $getCustomerAddressQuery = "select entity_id from $customerAddressTable where parent_id = $customerId";
            $addressId = $connection->fetchOne($getCustomerAddressQuery);

            $customerTable = $connection->getTableName('customer_entity');
            $getCustomerQuery = "update $customerTable set default_billing=$addressId ,default_shipping=$addressId where entity_id=$customerId";
            $connection->query($getCustomerQuery);
        }

        $customerObj = $this->customerSession->getCustomer();

        $shipwekonumber = ''; $shipDob = '';
        if($this->getCookieManager()->getCookie('shipWekoCard')){
            $shipwekonumber = $this->getCookieManager()->getCookie('shipWekoCard');
        }
        if($this->getCookieManager()->getCookie('shipDob')){
            $shipDob = $this->getCookieManager()->getCookie('shipDob');
        }
        /* check for registered customer and save weko card number and dob */        
        if($customerId){
            $customerAttribute = $this->customerRepository->getById($customerId);            
            
            // $isWekoNumberExist = $customerObj->getData('weko_card_number');
            // $isDobExist = $customerObj->getData('dob');

            if(!empty($shipwekonumber)){
                $wekoNumber = $shipwekonumber;            
                $customerAttribute->setCustomAttribute('weko_card_number',$wekoNumber);
            }

            if(!empty($shipDob)){
                /* dob is not an custom attribute its default attibute */
                $dob = $shipDob;
                $customerAttribute->setData('dob',date('Y-m-d H:i:s',strtotime($dob)) );
            }
            if(!empty($shipwekonumber) || !empty($shipDob)){
                $this->customerRepository->save($customerAttribute);
            }
        }    
        
        $orderId = $this->checkoutSession->getLastOrderId();
        $connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $tblSalesOrderAddress = $connection->getTableName('sales_order_address');
        if(!empty($shipwekonumber) || $customerId){
            if(empty($shipwekonumber)){
                $shipwekonumber = $customerObj->getData('weko_card_number');                
            }
            /* hard code for set customer info inside sales_order table */
            $connection->query("UPDATE `$tblSalesOrderAddress` set weko_card_number='".$shipwekonumber."' where parent_id=".$orderId." AND address_type='shipping' ");                
            /* delete cookie */
            $metadata = $this->getCookieMetadataFactory()->createCookieMetadata();
            $metadata->setPath('/');
            $this->getCookieManager()->deleteCookie('shipWekoCard', $metadata);
        }
        if(!empty($shipDob) || $customerId){
            if(empty($shipDob)){
                $savedate = $customerObj->getData('dob'); 
                if(!empty($savedate)){
                    $savedate_timestamp = strtotime($savedate);
                    $shipDob = date('m/d/Y', $savedate_timestamp); 
                }
            }
            /* hard code for set customer info inside sales_order table */                
            $connection->query("UPDATE `$tblSalesOrderAddress` set dob='".$shipDob."' where parent_id=".$orderId." AND address_type='shipping' ");                
            /* delete cookie */
            $metadata = $this->getCookieMetadataFactory()->createCookieMetadata();
            $metadata->setPath('/');
            $this->getCookieManager()->deleteCookie('shipDob', $metadata);
        }
        
        return $this;
    }

    private function getCookieManager()
    {
        if (!$this->cookieMetadataManager) {
            $this->cookieMetadataManager = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Stdlib\Cookie\PhpCookieManager::class
            );
        }
        return $this->cookieMetadataManager;
    }

    private function getCookieMetadataFactory()
    {
        if (!$this->cookieMetadataFactory) {
            $this->cookieMetadataFactory = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory::class
            );
        }
        return $this->cookieMetadataFactory;
    }

}