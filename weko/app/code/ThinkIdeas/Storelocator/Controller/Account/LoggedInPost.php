<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ThinkIdeas\Storelocator\Controller\Account;

use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\ScopeInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class LoggedInPost extends Action
{
    /** @var AccountManagementInterface */
    protected $customerAccountManagement;

    /** @var Validator */
    protected $formKeyValidator;

    /**
     * @var AccountRedirect
     */
    protected $accountRedirect;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    private $cookieMetadataManager;

    protected $_objectManager;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param AccountManagementInterface $customerAccountManagement
     * @param CustomerUrl $customerHelperData
     * @param Validator $formKeyValidator
     * @param AccountRedirect $accountRedirect
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        AccountManagementInterface $customerAccountManagement,
        CustomerUrl $customerHelperData,
        Validator $formKeyValidator,
        AccountRedirect $accountRedirect,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->session                   = $customerSession;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->customerUrl               = $customerHelperData;
        $this->formKeyValidator          = $formKeyValidator;
        $this->accountRedirect           = $accountRedirect;
        $this->_objectManager            = $context->getObjectManager();
        $this->resultJsonFactory         = $resultJsonFactory;

        parent::__construct($context);
    }


    /**
     * LoggedIn post action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        // $data = $this->getRequest()->getPostValue();
        $resultAjax = $this->resultJsonFactory->create();


        $billingId =  $this->session->getCustomer()->getDefaultBilling();
                        
        if ($billingId)
        {
            $address = $this->_objectManager->create('Magento\Customer\Model\Address')->load($billingId);

            $loginResponse = $address->getData();
        }
        
        $loginResponse['email'] = $this->session->getCustomer()->getEmail();
        $loginResponse['dbo'] = $this->session->getCustomer()->getDob();
        $loginResponse['gender'] = $this->session->getCustomer()->getGender();
        
        return $resultAjax->setData($loginResponse);
    }
}
