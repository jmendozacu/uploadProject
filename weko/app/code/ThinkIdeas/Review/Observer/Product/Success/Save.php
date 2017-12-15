<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ThinkIdeas\Review\Observer\Product\Success;

use Magento\Framework\Event\Observer as EventObserver;
use ThinkIdeas\Review\Model\UrlPersistInterface;
use Magento\Framework\Event\ObserverInterface;
 
 
class Save implements ObserverInterface
{
    
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var UrlPersistInterface
     */
    protected $urlPersist;

    /**
     * @param \ThinkIdeas\Review\Model\BrandUrlRewriteGenerator $brandUrlRewriteGenerator
     * @param UrlPersistInterface $urlPersist
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectmanager)
    {
        $this->_objectManager = $objectmanager;
    }

    /**
     * Generate urls for UrlRewrite and save it in storage
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {

        $orderId = $_POST['orderid'];
        $productId = $_POST['productid'];

       

        $reviewData = $this->_objectManager->create('ThinkIdeas\Review\Model\ResourceModel\Review\Collection')
                    ->addFieldToFilter('order_id', ['eq' => $orderId]);
             
        
        foreach ($reviewData as $key => $review) {
            $productIds = $review->getProductIds();
        }
        

        if (!empty($productIds)) 
        {
            $ids  = explode(',', $productIds);
            $ids[count($ids)] = $_POST['productid'];
            $pids = implode(',', $ids);
        }
        else
        {
            $pids = $_POST['productid'];
        }

        foreach ($reviewData as $key => $review) {
            $review->setProductIds($pids);
        }

        $reviewData->save();
        
    }
}