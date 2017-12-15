<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ThinkIdeas\Review\Block\Product;

use Magento\Store\Model\ScopeInterface;

/**
 * Block to render customer's gender attribute
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Reviews extends \Magento\Framework\View\Element\Template
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
    protected $_objectReview;
    protected $searchCriteriaBuilder;

    public function __construct(
         \Magento\Framework\View\Element\Template\Context $context,
         \Magento\Framework\ObjectManagerInterface $objectmanager,
         \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
         \Magento\Sales\Api\Data\OrderInterface $vorder,
         \Magento\Review\Model\RatingFactory $ratingFactory,
         \Magento\Review\Model\Review $reviewFactory,
         \Magento\Framework\Session\Generic $reviewSession,
         \Magento\Sales\Model\Order $order,
         \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
          parent::__construct($context); 
          $this->order           = $order;
          $this->_objectManager  = $objectmanager;        
          $this->orderRepository = $orderRepository;
          $this->_vorder         = $vorder;
          $this->_ratingFactory  = $ratingFactory;
          $this->reviewSession = $reviewSession;
          $this->_objectReview = $reviewFactory;
          $this->searchCriteriaBuilder = $searchCriteriaBuilder;
          $this->orderId = (int) $this->getRequest()->getParam('orderid');
    }

    public function getAllStart($pid = null) 
    {
        $pid = $this->getRequest()->getParam('id');
        $review = $this->_objectReview->getCollection()     
        //  \Magento\Review\Model\Review $reviewFactory (_objectReview)
                ->addFieldToFilter('main_table.status_id', 1)
                //$pid = > your current product ID
                ->addEntityFilter('product', $pid)          
                ->addStoreFilter($this->_storeManager->getStore()->getId())
                ->addFieldToSelect('review_id');

        // echo"<pre/>"; print_r($review->getData());exit;
        $review->getSelect()->columns('detail.detail_id')->joinInner(
                ['vote' => $review->getTable('rating_option_vote')], 'main_table.review_id = vote.review_id', array('review_value' => 'vote.value')
        );
        $review->getSelect()->order('review_value DESC');
        $review->getSelect()->columns('count(vote.vote_id) as total_vote')->group('review_value');
        for ($i = 5; $i >= 1; $i--) {
            $arrRatings[$i]['value'] = 0;
        }
        foreach ($review as $_result) {
            $arrRatings[$_result['review_value']]['value'] = $_result['total_vote'];
        }
        return $arrRatings;
    }

    public function isReviewAllowed($customerId = null)
    {
        $pid = $this->getRequest()->getParam('id');
        $reviews = $this->_objectReview->getCollection()     
        //  \Magento\Review\Model\Review $reviewFactory (_objectReview)
                ->addFieldToFilter('main_table.status_id', 1)
                //$pid = > your current product ID
                ->addEntityFilter('product', $pid)          
                ->addStoreFilter($this->_storeManager->getStore()->getId())
                ->addFieldToSelect('review_id');

        $customerSession = $this->_objectManager->get('Magento\Customer\Model\Session');
        if($customerSession->isLoggedIn()) {
            $cid = $customerSession->getCustomer()->getId();  // get Customer Id
            $isAllowed = true;

             $orderDatamodel = $this->_objectManager->get('Magento\Sales\Model\Order')->getCollection()
                            ->addFieldToFilter('customer_id', $cid);

            foreach($orderDatamodel as $orderDatamodel1){
            $getid =  $orderDatamodel1->getData("entity_id");
            $orderData = $this->_objectManager->create('Magento\Sales\Model\Order')->load($getid);

                  $getorderdata = $orderData->getData();
                  $orderItems = $orderData->getAllVisibleItems();
                  foreach($orderItems as $orderItem){                    
                        if ($orderItem->getProductId() == $pid)
                        {
                            return true;
                        }
                }
             }
        }

        return false;
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

    public function getProductRatings($id = null)
    {
        if (!$id) 
        {
            $id = $this->getRequest()->getParam('id');
        }
        
        $ratingOb = $this->_objectManager->create('Magento\Review\Model\Rating')->getEntitySummary($id);   

return $ratingOb;
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

        $date1 = strtotime(date('Y-m-d H:i:s'));
        $date2 = strtotime($time);

        $diff = $date1 - $date2;
        $hours = $diff / ( 60 * 60 );

        if(round(abs($hours)) > 2 )
        {
            return false;
        }
        return true;
    }
}
