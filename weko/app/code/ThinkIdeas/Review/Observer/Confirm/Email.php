<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ThinkIdeas\Review\Observer\Confirm;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Event\Observer as EventObserver;
use ThinkIdeas\Review\Model\UrlPersistInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Block to render customer's gender attribute
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Email implements ObserverInterface
{
    protected $_objectManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
   
    protected $_transportBuilder;
    
    protected $_customerRepositoryInterface;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        TransportBuilder $transportBuilder
    )
    {
        $this->_objectManager = $objectmanager;
        $this->_transportBuilder = $transportBuilder; 
        $this->_scopeConfig = $scopeConfig; 
        $this->_customerRepositoryInterface = $customerRepositoryInterface;    
    }

    /**
     * Email for review confirmation
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        $customerSession = $this->_objectManager->create('Magento\Customer\Model\Session');
            
        $emailTemplateVariables = array();
        $url = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
        ->getStore()
        ->getBaseUrl();
        
        $review = $observer->getEvent()->getReview();

        if ($review->getCustomerId())
        {
            $customer = $this->_customerRepositoryInterface->getById($review->getCustomerId());

            $emailTempVariables['url'] =$url;

            $senderName = $this->getStorename();

            $senderEmail = $this->getStoreEmail();

            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($emailTempVariables);

            $sender = [
                        'name' => $senderName,
                        'email' => $senderEmail,
                        ];
                        
            $email = $customer->getEmail();
            
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            
            /*Email Template Id*/
            $templateId = $this->getApprovedEmailTemplateId();
            
            $transport = $this->_transportBuilder->setTemplateIdentifier($templateId)
            ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID])
            ->setTemplateVars(['data' => $postObject])
            ->setFrom($sender)
            ->addTo($email)
            ->setReplyTo($senderEmail)            
            ->getTransport();               
            
            $transport->sendMessage();
        }
        
    }

    public function getApprovedEmailTemplateId($storeId = null)
    {
        return $this->_scopeConfig->getValue(
            'thinkideas_reviews_section/general_review_setting/product_review_confirmation_email_template',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getStorename()
    {
        return $this->_scopeConfig->getValue(
            'trans_email/ident_sales/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getStoreEmail()
    {
        return $this->_scopeConfig->getValue(
            'trans_email/ident_sales/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
