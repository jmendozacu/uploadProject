<?php
namespace ThinkIdeas\Additional\Block;

class WishlistCollection extends \Magento\Framework\View\Element\Template
{
    protected $_currentUserWishlistCollectionFactory ;
    protected $_Customersession;
    protected $wishlistProvider;
    protected $_productloader;
    protected $_objectManager;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_imageHelper;
    
    protected $helper;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory $currentUserWishlistCollectionFactory,
        \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider,
        \Magento\Customer\Model\Session $Customersession,
        \Magento\Catalog\Model\ProductFactory $productloader,
        \Magento\Framework\ObjectManagerInterface $objectmanager,


        array $data = []
    ) {
        $this->_Customersession                      = $Customersession;
        $this->_currentUserWishlistCollectionFactory = $currentUserWishlistCollectionFactory;
        $this->wishlistProvider                      = $wishlistProvider;
        $this->_productloader                        = $productloader;
        $this->_objectManager                        = $objectmanager;
        $this->_imageHelper                          = $context->getImageHelper();
        parent::__construct($context, $data);
    }

    public function getcurrentUserWishlistItems(){

        $collection = $this->_currentUserWishlistCollectionFactory->create();
        $collection->addCustomerIdFilter($this->_Customersession->getCustomerId());
        
        return $collection;

    }

    public function getProduct($id)
    {
        $product = $this->_objectManager->get('Magento\Catalog\Model\Product')->load($id);

        return $product;
    }

    public function getImageHtml($product)
    {
        $storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface'); 
        $currentStore = $storeManager->getStore();
        $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        
        $imgUrl = $this->_imageHelper->init($product, 'mini_cart_product_thumbnail')->getUrl();
        
        $imageHtml = "<img class='product-image-photo' src='" . $imgUrl ."' width='" . $product->getResizedImageWidth() . "' height='" . $product->getResizedImageHeight() . "' alt='" . $product->getName() . "' />";
        
        return $imageHtml;
 
    }
    public function getWishlistUrl()
    {
        return $this->getUrl('wishlist');
        
    }
}