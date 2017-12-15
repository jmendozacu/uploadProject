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
class Success extends \Magento\Framework\View\Element\Template
{
    protected $_objectManager;

    /**
     * Generic session
     *
     * @var \Magento\Framework\Session\Generic
     */
    protected $reviewSession;

    /**
     * @var ReviewRendererInterface
     */
    protected $_productsFactory;

    protected $_reviewFactory;

    public function __construct(
         \Magento\Framework\View\Element\Template\Context $context,
         \Magento\Framework\ObjectManagerInterface $objectmanager,
         \Magento\Review\Model\RatingFactory $ratingFactory,
         \Magento\Review\Model\ReviewFactory $reviewFactory,
         \Magento\Framework\Session\Generic $reviewSession,
         \Magento\Review\Model\ResourceModel\Review\Product\CollectionFactory $productsFactory,
          \ThinkIdeas\Review\Model\ResourceModel\Review\CollectionFactory $itemCollectionFactory

    )
    {
          parent::__construct($context); 
          $this->_objectManager  = $objectmanager;        
          $this->_ratingFactory  = $ratingFactory;
          $this->_reviewFactory = $reviewFactory;
          $this->reviewSession = $reviewSession;
          $this->_productsFactory = $productsFactory;

          $this->productId = (int) $this->getRequest()->getParam('productid');
          $this->orderId = (int) $this->getRequest()->getParam('orderid');
          $this->rating = (int) $this->getRequest()->getParam('rtg');
          $this->reviewId = (int) $this->getRequest()->getParam('rw');

        $this->_itemCollectionFactory = $itemCollectionFactory;
    }

    public function getOrderItemsReview()
    {

        $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($this->orderId);

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

    public function getRatingSum()
    {
            return $this->rating * 20; 

    }

    public function getReviewDetails()
    {
        $collection = $this->_productsFactory->create()
                    ->addEntityFilter($this->productId)
                    ->addAttributeToFilter('rt.review_id', $this->reviewId);

        return $collection;
    }


    /**
     * Get review product post action
     *
     * @return string
     */
    public function getAction()
    {   
        return $this->getUrl(
            'review/orders/products',
            [
                '_secure'   => $this->getRequest()->isSecure(),
                'orderid'   => $this->orderId,
                'productid' => $this->productId,
                'rtg'       => $this->rating,
                'rw'        => $this->reviewId
            ]
        );
    }
}
