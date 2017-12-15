<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ThinkIdeas\Review\Observer\Order;

use Magento\Framework\Event\Observer as EventObserver;
use ThinkIdeas\Review\Model\UrlPersistInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\ScopeInterface;
 
 
class Success implements ObserverInterface
{
    
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var UrlPersistInterface
     */
    protected $urlPersist;

    protected $_transportBuilder;

    /** 
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection
     */
   protected $orders;

   /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @param \ThinkIdeas\Review\Model\BrandUrlRewriteGenerator $brandUrlRewriteGenerator
     * @param UrlPersistInterface $urlPersist
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
        )
    {
        $this->_objectManager = $objectmanager;
        $this->_scopeConfig = $scopeConfig;
        $this->_transportBuilder = $transportBuilder;
        $this->_orderCollectionFactory = $orderCollectionFactory;
    }

    /**
     * Generate urls for UrlRewrite and save it in storage
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {

        $customerSession = $this->_objectManager->create('Magento\Customer\Model\Session');

        $days = $this->_scopeConfig->getValue('thinkideas_reviews_section/general_review_setting/email_review_days', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $currentDate = date("Y-m-d H:i:s");
        // $fromDate = '2016-01-01 00:00:01';
        // $toDate = '2017-01-02 23:59:59';
        $toDate = date('Y-m-d 23:59:59', strtotime($currentDate . ' -' . $days . ' day'));
        $fromDate = date('Y-m-d 00:00:01', strtotime($currentDate . ' -' . $days . ' day'));

        if (!$this->orders) {
            $this->orders = $this->_orderCollectionFactory->create()
                ->addFieldToSelect('*')
                ->addAttributeToFilter('created_at', array('from' => $fromDate, 'to' => $toDate))
                ->setOrder('customer_id', 'ASC');
        }

        $data = array();

        foreach ($this->orders as $key => $order) {
            $emailTemplateVariables = array();
            $customerFirstName = $order->getCustomerFirstname();
            $customerLastName = $order->getCustomerLastname();
            $url = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
                ->getStore()
                ->getBaseUrl();

            $emailTempVariables['orderid'] = $order->getEntityId();
            $emailTempVariables['firstname'] = $customerFirstName;
            $emailTempVariables['lastname'] = $customerLastName;


            $senderName = $this->_scopeConfig->getValue('trans_email/ident_general/name',ScopeInterface::SCOPE_STORE);

            $senderEmail = $this->_scopeConfig->getValue('trans_email/ident_general/email',ScopeInterface::SCOPE_STORE);

            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($emailTempVariables);

            $sender = [
                'name' => $senderName,
                'email' => $senderEmail,
            ];
            $email = $order->getCustomerEmail();

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

            $rowData = $this->_objectManager->create('ThinkIdeas\Review\Model\Review');

            $data['customer_id'] = $order->getCustomerId();
            $data['order_id'] = $order->getEntityId();
            $data['email_sent_time'] = date('Y-m-d H:i:s');
            $data['status'] = 1;

            $rowData->setData($data);

            $rowData->save();
        }


    }

    public function getApprovedEmailTemplateId($storeId = null)
    {
        return $this->_scopeConfig->getValue(
            'thinkideas_reviews_section/general_review_setting/product_review_email_template',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}