<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ThinkIdeas\Review\Controller\Orders;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Products extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    protected $_objectManager;

    /**
    * @var \Magento\Framework\App\Config\ScopeConfigInterface
    */
    protected $_scopeConfig;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->resultPageFactory = $resultPageFactory;
                
        parent::__construct($context);

        $this->_objectManager = $context->getObjectManager();
        $this->_scopeConfig = $scopeConfig;
        $this->orderId = (int) $this->getRequest()->getParam('orderid');
    }

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $isAllowed = $this->_isAllowed();

         /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        if ($this->_isExpired()) {
            $resultPage->getConfig()->getTitle()->set(__('Review your purchased products'));
        }
        else
        {
            $resultPage->getConfig()->getTitle()->set(__('Your link has been expired!'));
        }
        
        return $resultPage;
    }

    /**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
    protected function _isExpired()
    {        
        $time = "";
        $hoursSet = $this->_scopeConfig->getValue('thinkideas_reviews_section/general_review_setting/order_review_link_expiration_period', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);  

        $reviewData = $this->_objectManager->create('ThinkIdeas\Review\Model\ResourceModel\Review\Collection')
                    ->addFieldToFilter('order_id', ['eq' => $this->orderId]);
        
        foreach ($reviewData as $key => $review) {
            $time = $review->getEmailSentTime();
        }

        if ($time) {
            $date1 = strtotime(date('Y-m-d H:i:s'));
            $date2 = strtotime($time);

            $diff = $date1 - $date2;
            $hours = $diff / ( 60 * 60 );

            if(round(abs($hours)) > $hoursSet )
            {
                return false;
            }
        }
            
        return true;
    }

    /**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
    public function _isAllowed()
    {        
        $customerSession = $this->_objectManager->get('Magento\Customer\Model\Session');
        $urlInterface = $this->_objectManager->get('\Magento\Framework\UrlInterface');
        
        if($customerSession->isLoggedIn()) {
            return true;    
        }
        else
        {
            
            $customerSession->setAfterAuthUrl($urlInterface->getCurrentUrl());
            
            $customerSession->authenticate();
        }
    }
}
