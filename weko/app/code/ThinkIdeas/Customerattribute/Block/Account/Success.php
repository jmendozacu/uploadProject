<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ThinkIdeas\Customerattribute\Block\Account;

/**
 * Block to render customer's gender attribute
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Success extends \Magento\Framework\View\Element\Template
{
    protected $_objectManager;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectmanager
    )
    {
        $this->_objectManager = $objectmanager;

        parent::__construct($context);        
    }

    public function sayHello()
    {
        return __('Hello World');
    }

    public function getCurrentUser()
    {
        $customerSession = $this->_objectManager->create('Magento\Customer\Model\Session');

        if($customerSession->isLoggedIn()) {
            echo   $customerSession->getCustomer()->getId()."<br/>";  // get Customer Id
            echo   $customerSession->getCustomer()->getName()."<br/>";  // get  Full Name
            echo   $customerSession->getCustomer()->getEmail()."<br/>"; // get Email Name
            echo   $customerSession->getCustomer()->getGroupId()."<br/>";  // get Customer Group Id
        }  exit;
    }

}
