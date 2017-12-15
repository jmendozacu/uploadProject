<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace ThinkIdeas\Storelocator\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use ThinkIdeas\Storelocator\Model\ResourceModel\Stores\CollectionFactory as StorelocatorCollectionFactory;
use ThinkIdeas\Storelocator\Model\Stores;

class Index extends Action
{
    /**
     * @var string
     */
    const META_DESCRIPTION_CONFIG_PATH = 'thinkideas_storelocator/stockist_content/meta_description';
    
    /**
     * @var string
     */
    const META_KEYWORDS_CONFIG_PATH = 'thinkideas_storelocator/stockist_content/meta_keywords';
    
    /**
     * @var string
     */
    const META_TITLE_CONFIG_PATH = 'thinkideas_storelocator/stockist_content/meta_title';
    
    /**
     * @var string
     */
    const BREADCRUMBS_CONFIG_PATH = 'thinkideas_storelocator/stockist_content/breadcrumbs';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;
    
    /** @var \Magento\Framework\View\Result\PageFactory  */
    public $resultPageFactory;

    public $_registry;

    protected $catalogSession;

    /**
     * @var StorelocatorCollectionFactory
     */
    public $storelocatorCollectionFactory;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    protected $_checkoutSession;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Registry $registry,
        StorelocatorCollectionFactory $storelocatorCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->scopeConfig = $scopeConfig;
        $this->_registry = $registry;
        $this->catalogSession = $catalogSession;
        $this->storelocatorCollectionFactory = $storelocatorCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->_checkoutSession              = $checkoutSession;
    }
    
    /**
     * Load the page defined in view/frontend/layout/storelocator_index_index.xml
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $productData = $this->getRequest()->getPostValue();
        $this->_registry->register('reserved_product', $productData);

        if ($productData) 
        {   
            
            if (!$this->_checkoutSession->getData("reserved")) {
                $this->_checkoutSession->setData("reserved", $productData);    
            }
            else
            {
                $getProductSession = $this->_checkoutSession->getData("reserved");    
                if ($productData['product'] !=  $getProductSession['product']) 
                {
                    $this->_checkoutSession->setData("reserved", $productData);
                }
            }
        }
        else
        {
            $resultRedirect->setPath('/');
            return $resultRedirect;
        }
        

        

        /* ajax call to get store name */    
        $stockistId = $this->getRequest()->getParam('stockistid', false);

        if (isset($stockistId) && !empty($stockistId)) {
            $this->getStoresForFrontend($stockistId);    
        }
        
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(
            $this->scopeConfig->getValue(self::META_TITLE_CONFIG_PATH, ScopeInterface::SCOPE_STORE)
        );
        $resultPage->getConfig()->setDescription(
            $this->scopeConfig->getValue(self::META_DESCRIPTION_CONFIG_PATH, ScopeInterface::SCOPE_STORE)
        );
        $resultPage->getConfig()->setKeywords(
            $this->scopeConfig->getValue(self::META_KEYWORDS_CONFIG_PATH, ScopeInterface::SCOPE_STORE)
        );
        if ($this->scopeConfig->isSetFlag(self::BREADCRUMBS_CONFIG_PATH, ScopeInterface::SCOPE_STORE)) {
            
            /** @var \Magento\Theme\Block\Html\Breadcrumbs $breadcrumbsBlock */
            $breadcrumbsBlock = $resultPage->getLayout()->getBlock('breadcrumbs');
            if ($breadcrumbsBlock) {
                $breadcrumbsBlock->addCrumb(
                    'home',
                    [
                        'label'    => __('Home'),
                        'link'     => $this->_url->getUrl('')
                    ]
                );
                $breadcrumbsBlock->addCrumb(
                    'storelocator',
                    [
                        'label'    => __('Storelocator'),
                    ]
                );
            }
        }
        
        return $resultPage;
    }

    protected function getStoresForFrontend($stockistId)
    {
        /*Put below your code*/
        // $stockistId = $this->getRequest()->getParam('stockistid', false);

        $collection = $this->storelocatorCollectionFactory->create()
            ->addFieldToSelect('name')
            ->addFieldToFilter('stockist_id', $stockistId)
            ->addFieldToFilter('status', Stores::STATUS_ENABLED)
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->setOrder('stockist_id', 'ASC');

        
        return $collection->getData();
    }

    protected function setSessionData($key, $value)
    {
        return $this->catalogSession->setData($key, $value);
    }

    protected function getSessionData($key, $remove = false)
    {
        return $this->catalogSession->getData($key, $remove);
    }

}
