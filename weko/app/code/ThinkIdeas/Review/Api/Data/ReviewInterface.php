<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ThinkIdeas\Review\Api\Data;

/**
 * Static Banner interface.
 * @api
 */
interface ReviewInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const REVIEWID     = 'review_id';
    const CUSTOMERID   = 'customer_id';
    const ORDERID      = 'order_id';
    const PRODUCTIDS   = 'product_ids';
    const SENTEMALTIME = 'email_sent_time';
    const STATUS       = 'status';

    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getReviewId();

    /**
     * Get title
     *
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Get valid from date
     *
     * @return int|null
     */
    public function getOrderId();

    /**
     * Get Product Ids
     *
     * @return string|null
     */
    public function getProductIds();
    
    /**
     * Get valid to date
     *
     * @return string|null
     */
    public function getSentEmailTime();

    /**
     * Get banner image
     *
     * @return int|null
     */
    public function getStatus();


    /**
     * Set ID
     *
     * @param int $reviewId
     * @return ReviewInterface
     */
    public function setReviewId($reviewId);

    /**
     * Set Customer Id
     *
     * @param int $customerId
     * @return ReviewInterface
     */
    public function setCustomerId($customerId);

    /**
     * Set Order Id
     *
     * @param int $orderId
     * @return ReviewInterface
     */
    public function setOrderId($orderId);

    /**
     * Set Product Ids
     *
     * @param string $productIds
     * @return ReviewInterface
     */
    public function setProductIds($productIds);

    /**
     * Set Email sent Time
     *
     * @param string $sentEmailTime
     * @return ReviewInterface
     */
    public function setSentEmailTime($sentEmailTime);

    /**
     * Set Status
     *
     * @param int $status
     * @return ReviewInterface
     */
    public function setStatus($status);
}
