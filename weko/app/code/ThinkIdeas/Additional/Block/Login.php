<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * "My Wish List" link
 */
namespace ThinkIdeas\Additional\Block;

/**
 * Class Link
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Login extends \Magento\Customer\Block\Form\Login
{
    /**
     * @var \Magento\Wishlist\Helper\Data
     */
    protected $_wishlistHelper;
    protected $_objectManager;
    protected $_currentUserWishlistCollectionFactory ;

    /**
     * @var int
     */
    private $_username = -1;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $_customerUrl;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory $currentUserWishlistCollectionFactory,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        array $data = []
    ) {
        parent::__construct($context, $customerSession, $customerUrl, $data);
        $this->_isScopePrivate                       = true;
        $this->_customerUrl                          = $customerUrl;
        $this->_customerSession                      = $customerSession;
        $this->_currentUserWishlistCollectionFactory = $currentUserWishlistCollectionFactory;
        $this->_objectManager                        = $objectmanager;
    }

    public function getcurrentUserWishlistItems(){
        // var_dump($this->_customerSession->getCustomer()->getId());
        // exit();
        $collection = $this->_currentUserWishlistCollectionFactory->create();
        $collection->addCustomerIdFilter($this->_customerSession->getCustomerId());

        return $collection;

    }

    public function getProduct($id)
    {
        $product = $this->_objectManager->get('Magento\Catalog\Model\Product')->load($id);

        return $product;
    }

    public function getImageUrl($image)
    {
        $storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface'); 
        $currentStore = $storeManager->getStore();
        $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $imgUrl = $mediaUrl . "catalog/product". $image;
        
        return $imgUrl;
 
    }
}
