<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace ThinkIdeas\Storelocator\Block;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use ThinkIdeas\Storelocator\Model\ResourceModel\Stores\CollectionFactory as StorelocatorCollectionFactory;
use ThinkIdeas\Storelocator\Model\Stores;

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
         \Magento\Review\Model\RatingFactory $ratingFactory,
         \Magento\Review\Model\ReviewFactory $reviewFactory,
         \Magento\Framework\Session\Generic $reviewSession,
         \Magento\Review\Model\ResourceModel\Review\Product\CollectionFactory $productsFactory,
         \ThinkIdeas\Review\Model\ResourceModel\Review\CollectionFactory $itemCollectionFactory,
         \Magento\Framework\ObjectManagerInterface $objectmanager
    )
    {
          parent::__construct($context); 
          $this->_objectManager  = $objectmanager;        
          $this->_ratingFactory  = $ratingFactory;
          $this->_reviewFactory = $reviewFactory;
          $this->reviewSession = $reviewSession;
          $this->_productsFactory = $productsFactory;;
    }
}
