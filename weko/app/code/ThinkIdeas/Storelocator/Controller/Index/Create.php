<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ThinkIdeas\Storelocator\Controller\Index;

use Magento\Framework\Exception\PaymentException;
use ThinkIdeas\Storelocator\Model\ResourceModel\Stores\CollectionFactory as StorelocatorCollectionFactory;
use ThinkIdeas\Storelocator\Model\Stores;
use Magento\Framework\Mail\Template\TransportBuilder;

class Create extends \Magento\Framework\App\Action\Action
{
    

    //params
    protected $_paymentMethod = 'cashondelivery';
    protected $_shippingMethod = 'flatrate_flatrate';
    protected $_storeId = null;//e.g. 1
    protected $_emails = array(
//        'roni_cost@example.com',
//        'test@example.com',
    );
    protected $_days = 0;//e.g. 20; random if 0

    /** @var CustomerRepositoryInterface */
    protected $_customerRepository;

    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $_objectManager;

    /** @var \Magento\Customer\Api\Data\CustomerInterfaceFactory */
    protected $_customerFactory;

    /** @var  \Magento\Store\Model\StoreManagerInterface */
    protected $_storeManager;

    /** @var mixed \Magento\Catalog\Model\ProductFactory */
    protected $_productFactory;

    /** @var mixed \Magento\Quote\Api\CartRepositoryInterface */
    protected $_cartRepositoryInterface;

    /** @var mixed \Magento\Quote\Model\Quote\Address\Rate */
    protected $_shippingRate;

    protected $quote;

    protected $cartManagementInterface;

    protected $scopeConfig;

    protected $orderRepository;

    protected $_transportBuilder;

    /**
     * @var StorelocatorCollectionFactory
     */
    public $storelocatorCollectionFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        StorelocatorCollectionFactory $storelocatorCollectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Magento\Quote\Model\Quote\Address\Rate $rate,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        TransportBuilder $transportBuilder
    )
    {
        $this->_storeManager                 = $storeManager;
        $this->_scopeConfig                  = $scopeConfig;
        $this->_customerFactory              = $customerFactory;
        $this->storelocatorCollectionFactory = $storelocatorCollectionFactory;
        $this->_objectManager                = $context->getObjectManager();
        $this->_productFactory               = $productFactory;
        $this->_customerRepository           = $customerRepository;
        $this->_cartRepositoryInterface      = $cartRepository;
        $this->_shippingRate                 = $rate;
        $this->orderRepository               = $orderRepository;
        $this->_transportBuilder             = $transportBuilder;
        parent::__construct($context);
    }


    /**
     * Saving quote and create order
     *
     * @return \Magento\Backend\Model\View\Result\Forward|\Magento\Backend\Model\View\Result\Redirect
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        
         // echo"<pre/>"; print_r($data);exit;
                $product = $data['product'];

                $items = [];
                $items[] = ['product_id' => $product, 'qty' => $data['qty']];
                // echo"<pre/>"; print_r($data);exit;
                $email = $data['email'];
                if (!empty($data['password'])) 
                {
                   $tempOrder = [
                        'currency_id' => 'EUR',
                        'email' => $email, //buyer email id
                        'shipping_address' => [
                            'prefix' => $data['prefix'],
                            'firstname' => $data['firstname'], //address Details
                            'lastname' => $data['lastname'],
                            'street' => $data['street'][0] . " " . $data['street'][1],
                            'city' => $data['city'],
                            'country_id' => $data['country_id'],
                            'postcode' => $data['postcode'],
                            'telephone' => $data['telephone'],
                            'save_in_address_book' => 1
                        ],
                        'gender' => $data['gender'],
                        'items' => $items,                        
                        'password'=> $data['password']
                    ]; 
                }
                else{
                    $tempOrder = [
                        'currency_id' => 'EUR',
                        'email' => $email, //buyer email id
                        'shipping_address' => [
                            'prefix' => $data['prefix'],
                            'firstname' => $data['firstname'], //address Details
                            'lastname' => $data['lastname'],
                            'street' => $data['street'][0] . " " . $data['street'][1],
                            'city' => $data['city'],
                            'country_id' => $data['country_id'],
                            'postcode' => $data['postcode'],
                            'telephone' => $data['telephone'],
                            'save_in_address_book' => 1
                        ],
                        'gender' => $data['gender'],
                        'items' => $items
                    ];
                }   
                

                // echo"<pre/>"; print_r($tempOrder);exit;
                if($orderId = $this->createOrder($tempOrder))
                {
                    $order = $this->orderRepository->get($orderId);
                    // echo"<pre/>"; print_r($data);exit;
                    $orderIncrementId = $order->getIncrementId();

                    $data['orderno'] = "# " . $orderIncrementId;
                    $this->emailToStoreManager($data, $order);
                }
                $this->messageManager->addSuccess(__('You created the order.'));

                $resultRedirect->setPath('*/index/success');
                return $resultRedirect;
         
    }

    public function emailToStoreManager($data, $order)
    {
        $emailTemplateVariables = array();
        
        $stockistRef = $this->getStockist($data['stockist_id'])->getData();  
         // echo"<pre/>"; print_r($stockistRef);exit;
        $stockist = $stockistRef[0];

        $customerSession = $this->_objectManager->get('Magento\Customer\Model\Session');
        
        $data['customer'] = $customerSession->getCustomer()->getName();

        $email =  $stockist['email'];
        // echo"<pre/>"; print_r($data);exit;
        $msgVars['order']       = $order;
        $msgVars['stockist']    = $stockist['name'];
        $msgVars['orderno']     = $data['orderno'];
        $msgVars['productname'] = $data['productName'];
        $msgVars['productqty']  = $data['qty'];

        $senderName = $this->getStorename();

        $senderEmail = $this->getStoreEmail();

        $postObject = new \Magento\Framework\DataObject();
        $postObject->setData($msgVars);

        $sender = [
                    'name' => $senderName,
                    'email' => $senderEmail,
                    ];

        /*Email Template Id*/
            $templateId = 'reserved_order_email_template';
            
            $transport = $this->_transportBuilder->setTemplateIdentifier($templateId)
            ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID])
            ->setTemplateVars(['data' => $postObject])
            ->setFrom($sender)
            ->addTo($email)
            ->setReplyTo($senderEmail)            
            ->getTransport();               
            
            try
            {
                $transport->sendMessage(); 
            }
            catch (\Exception $e) 
            {
                $this->messageManager->addError(__('E-Mail-Prozess fehlgeschlagen.'));
            }
            
        return;
    }

    public function getStoreEmail()
    {
        return $this->_scopeConfig->getValue(
            'trans_email/ident_sales/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getStockist($stockistId)
    {
        $collection = $this->storelocatorCollectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('status', Stores::STATUS_ENABLED)
            ->addFieldToFilter('stockist_id', $stockistId)
            ->addStoreFilter($this->_storeManager->getStore()->getId());

        return $collection;
    }

    public function getStorename()
    {
        return $this->_scopeConfig->getValue(
            'trans_email/ident_sales/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function createOrder($orderData)
    {
        $this->cartManagementInterface = $this->_objectManager->get('\Magento\Quote\Api\CartManagementInterface');

        //init the store id and website id
        $store = $this->_storeManager->getStore($this->_storeId);
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();

        //init the customer
        $customer = $this->_customerFactory->create();
        $customer->setWebsiteId($websiteId);
        $customer->loadByEmail($orderData['email']);// load customer by email address
        // echo"<pre/>"; print_r($customer->getname());exit;
        //check the customer
         
        if (!$customer->getEntityId()) {

            //If not available then create this customer
            $customer->setWebsiteId($websiteId)
                ->setStore($store)
                ->setPrefix($orderData['shipping_address']['prefix'])
                ->setFirstname($orderData['shipping_address']['firstname'])
                ->setLastname($orderData['shipping_address']['lastname'])
                ->setEmail($orderData['email'])
                ->setPassword($orderData['password'])
                ->setGender($orderData['gender']);

            $customer->save();
        }
        
        //init the quote
        $cart_id = $this->cartManagementInterface->createEmptyCart();
        $cart = $this->_cartRepositoryInterface->get($cart_id);

        $cart->setStore($store);

        $cart->setCreatedAt('2015-09-15 08:23:17');

        // if you have already buyer id then you can load customer directly
        $customer = $this->_customerRepository->getById($customer->getEntityId());
        $cart->setCurrency();
        $cart->assignCustomer($customer); //Assign quote to customer

        //add items in quote
        foreach ($orderData['items'] as $item) {
            $product = $this->_productFactory->create()->load($item['product_id']);
            $cart->addProduct(
                $product,
                intval($item['qty'])
            );
        }

        if (!empty($this->_emails)) {
            $orderData = $this->_prepareExistingCustomer($customer, $orderData);
        }

        //Set Address to quote
        $cart->getBillingAddress()->addData($orderData['shipping_address']);
        $cart->getShippingAddress()->addData($orderData['shipping_address']);

        // Collect Rates and Set Shipping & Payment Method
        $this->_shippingRate
            ->setCode('freeshipping_freeshipping')
            ->getPrice(1);

        $shippingAddress = $cart->getShippingAddress();

        $shippingAddress->setCollectShippingRates(true)
            ->collectShippingRates()
            ->setShippingMethod($this->_shippingMethod); //shipping method
        $cart->getShippingAddress()->addShippingRate($this->_shippingRate);

        $cart->setPaymentMethod($this->_paymentMethod); //payment method

        $cart->setInventoryProcessed(false);

        // Set sales order payment
        $cart->getPayment()->importData(['method' => 'cashondelivery']);

        // Collect total and saeve
        $cart->collectTotals();

        // Submit the quote and create the order
        $cart->save();
        $cart = $this->_cartRepositoryInterface->get($cart->getId());
        $order_id = $this->cartManagementInterface->placeOrder($cart->getId());


        $resource = $this->_objectManager->get('\Magento\Framework\App\ResourceConnection');

        $connection = $resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $tblSalesOrder = $connection->getTableName('sales_order');

        if ($this->_days > 0) {
            $days = $this->_days;
        } else {
            $days = rand(0, 400);
        }

        $result = $connection->rawQuery('UPDATE `' . $tblSalesOrder . '` SET created_at = 
        (NOW() - interval ' . $days . ' day) WHERE entity_id=' . $order_id);

        return $order_id;
    }
    protected function _prepareExistingCustomer($customer, $orderData)
    {
        if ($customer->getFirstname()) {
            $orderData['shipping_address']['firstname'] = $customer->getFirstname();
        }

        if ($customer->getLastname()) {
            $orderData['shipping_address']['lastname'] = $customer->getLastname();
        }

        $addresses = $customer->getAddresses();

        if ($addresses[0]->getStreet()) {
            $orderData['shipping_address']['street'] = $addresses[0]->getStreet();
        }

        if ($addresses[0]->getCity()) {
            $orderData['shipping_address']['city'] = $addresses[0]->getCity();
        }

        if ($addresses[0]->getCountryId()) {
            $orderData['shipping_address']['country_id'] = $addresses[0]->getCountryId();
        }

        if ($addresses[0]->getRegion()) {
            $orderData['shipping_address']['region'] = $addresses[0]->getRegion();
        }

        if ($addresses[0]->getPostcode()) {
            $orderData['shipping_address']['postcode'] = $addresses[0]->getPostcode();
        }

        if ($addresses[0]->getTelephone()) {
            $orderData['shipping_address']['telephone'] = $addresses[0]->getTelephone();
        }

        if ($addresses[0]->getFax()) {
            $orderData['shipping_address']['fax'] = $addresses[0]->getFax();
        }

        return $orderData;
    }

    protected function getSimpleIds()
    {
        $this->_objectManager->get('\Magento\Framework\App\State')->setAreaCode('adminhtml');
        $resource = $this->_objectManager->get('\Magento\Framework\App\ResourceConnection');

        $connection = $resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $tblSalesOrder = $connection->getTableName('catalog_product_entity');

        $simpleProducts = $connection->fetchAll("SELECT entity_id FROM $tblSalesOrder WHERE type_id = 'simple'");
        $return = [];
        foreach ($simpleProducts as $simpleProduct) {
            $return[] = $simpleProduct['entity_id'];
        }
        return $return;
    }
}
