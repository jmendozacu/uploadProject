<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ThinkIdeas\Review\Block\Order;

use Magento\Store\Model\ScopeInterface;

/**
 * Block to render customer's gender attribute
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Products extends \Magento\Framework\View\Element\Template
{
    protected $_objectManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    protected $orderRepository;
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory 
     */
    protected $_vorder;
    protected $_orderCollectionFactory;

    /** 
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection
     */
   protected $orders;

       /**
     * Rating model
     *
     * @var \Magento\Review\Model\RatingFactory
     */
    protected $_ratingFactory;

    public $orderId;
    /**
     * Generic session
     *
     * @var \Magento\Framework\Session\Generic
     */
    protected $reviewSession;

    /**
     * @var \Magento\Catalog\Block\Product\ImageBuilder
     */
    protected $imageBuilder;

    public function __construct(
         \Magento\Framework\View\Element\Template\Context $context,
         \Magento\Framework\ObjectManagerInterface $objectmanager,
         \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
         \Magento\Sales\Api\Data\OrderInterface $vorder,
         \Magento\Review\Model\RatingFactory $ratingFactory,
         \Magento\Framework\Session\Generic $reviewSession,
         \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder,
         \Magento\Sales\Model\Order $order
    )
    {
          parent::__construct($context); 
          $this->order           = $order;
          $this->_objectManager  = $objectmanager;        
          $this->orderRepository = $orderRepository;
          $this->_vorder         = $vorder;
          $this->_ratingFactory  = $ratingFactory;
          $this->reviewSession = $reviewSession;
          $this->imageBuilder = $imageBuilder;
          
          $this->orderId = (int) $this->getRequest()->getParam('orderid');
    }

    public function getOrderItemsReview()
    {

        $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($this->orderId);

        // echo"<pre/>"; print_r($order->getData());exit;
        return $order->getAllVisibleItems();
    }

    public function getCustomerName()
    {
        $order = $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($this->orderId);

        return $order->getCustomerFirstname();
    }

    public function getLoadProduct($id)
    {
        $product = $this->_objectManager->get('Magento\Catalog\Model\Product')->load($id);

        return $product;
    }

    public function getProductRatings($id)
    {
          $ratingOb = $this->_objectManager->create('Magento\Review\Model\Rating')->getEntitySummary($id);   

          return $ratingOb;
    }

    /**
     * Retrieve product image
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $imageId
     * @param array $attributes
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function getImage($product, $imageId, $attributes = [])
    {
        return $this->imageBuilder->setProduct($product)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->create();
    }

    /**
     * Get collection of ratings
     *
     * @return RatingCollection
     */
    public function getRatings()
    {
        return $this->_ratingFactory->create()->getResourceCollection()->addEntityFilter(
            'product'
        )->setPositionOrder()->addRatingPerStoreName(
            $this->_storeManager->getStore()->getId()
        )->setStoreFilter(
            $this->_storeManager->getStore()->getId()
        )->setActiveFilter(
            true
        )->load()->addOptionToItems();
    }

    public function getReviewedPoducts()
    {
        
        $reviewData = $this->_objectManager->create('ThinkIdeas\Review\Model\ResourceModel\Review\Collection')
                    ->addFieldToFilter('order_id', ['eq' => $this->orderId]);

        
        foreach ($reviewData as $key => $review) {
            $productIds = $review->getProductIds();

            return $productIds;
        }

        return false;

    }

    /**
     * Get review product post action
     *
     * @return string
     */
    public function getAction()
    {   
        return $this->getUrl(
            'review/product/post',
            [
                '_secure' => $this->getRequest()->isSecure()
            ]
        );
    }

    /**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
    public function _isExpired()
    {        
        $time = "";
        $hours = $this->_scopeConfig->getValue('thinkideas_reviews_section/general_review_setting/order_review_link_expiration_period', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);  

        // echo"<pre/>"; print_r($this->orderId);exit;
        $reviewData = $this->_objectManager->create('ThinkIdeas\Review\Model\ResourceModel\Review\Collection')
                    ->addFieldToFilter('order_id', ['eq' => $this->orderId]);
        
        foreach ($reviewData as $key => $review) {
            $time = $review->getEmailSentTime();
        }


        if ($time)
        { 
            $date1 = strtotime(date('Y-m-d H:i:s'));
            $date2 = strtotime($time);

            $diff = $date1 - $date2;
            $hours = $diff / ( 60 * 60 );

            if(round(abs($hours)) > 2 )
            {
                return false;
            }
        }

        
        return true;
    }
}
