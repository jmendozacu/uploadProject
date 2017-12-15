<?php
namespace ThinkIdeas\Customerlogin\Observer;

use Magento\Framework\Event\ObserverInterface;

class CustomerLogin implements ObserverInterface
{
    protected $orderFactory;

    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\AddressFactory $address
    ) {
        $this->orderFactory = $orderFactory;
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->_customerSession = $customerSession;
        $this->address = $address;
    }

    /**
     * Set Email based on postcode location
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {       
        $order = $observer->getEvent()->getOrder(); 
        /*
        * Check for Order is virtual or not...
        */
        if (empty($order->getShippingAddress()) ){
            return;
        }
        /*check order is from frontend? */
        $orderFromFront = $order->getRemoteIp();
        $guestCustomer = $order->getCustomerIsGuest();
        if($orderFromFront && $guestCustomer){
            $websiteId = $this->storeManager->getWebsite()->getWebsiteId();
            $storeId = $this->storeManager->getStore()->getStoreId();
            $email = $order->getCustomerEmail();
            $firstname = $order->getShippingAddress()->getFirstname();
            $lastname = $order->getShippingAddress()->getLastname();
            $prefix = $order->getShippingAddress()->getPrefix();
            $shipping = $order->getShippingAddress();

            $customer = $this->customerFactory->create();
            $customer->setWebsiteId($websiteId);
            $customer->setEmail($email);
            $customer->setFirstname($firstname);
            $customer->setLastname($lastname);
            $newpassword = $this->rand_string(12);
            $customer->setPassword($newpassword);
            $customer->setConfirmation($newpassword);           
            $customer->setPrefix($prefix);
            $customer->setGroupId(1);
            $customer->save();
            //get last created customer id 
            $customerId = $customer->getId();

            try{
                $customer->sendNewAccountEmail();
            }catch (Exception $e) {
                return $e->getMessage();
            }
            
            $address = $this->address->create();
            $address->setCustomerId($customerId)
                    ->setPrefix($prefix)
                    ->setFirstname($firstname)
                    ->setLastname($lastname)
                    ->setCountryId($shipping->getCountryId())
                    ->setPostcode($shipping->getPostcode())
                    ->setCity($shipping->getCity())
                    ->setTelephone($shipping->getTelephone())
                    ->setCompany($shipping->getCompany())
                    ->setStreet($shipping->getStreet())
                    ->setRegion($shipping->getRegion())
                    ->setRegionId($shipping->getRegionId())
                    ->setIsDefaultBilling('1')
                    ->setIsDefaultShipping('1')
                    ->setSaveInAddressBook('1');
            $address->save();

            /* for customer to do logged in forcefully */
            $this->_customerSession->setCustomerAsLoggedIn($customer);

        }else{
            /* if order from backend nothing to do*/
            return;
        }        
    }

    public function rand_string($length) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        return substr(str_shuffle($chars), 0, $length);
    }
    
}