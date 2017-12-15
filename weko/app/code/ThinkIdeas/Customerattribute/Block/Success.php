<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ThinkIdeas\Customerattribute\Block;

/**
 * Block to render customer's gender attribute
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Success extends \Magento\Framework\View\Element\Template
{
    protected $_objectManager;
    protected $_storeManager;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectmanager
    )
    {
        $this->_objectManager = $objectmanager;
        $this->_storeManager  = $context->getStoreManager();

        parent::__construct($context);        
    }

    public function getHomepageUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    public function getCurrentUser()
    {
        // echo"<pre/>"; print_r("test");exit;
        $customerSession = $this->_objectManager->create('Magento\Customer\Model\Session');

        if($customerSession->isLoggedIn()) {
           
            return $customerSession->getCustomer()->getName();
        }
    }
}
