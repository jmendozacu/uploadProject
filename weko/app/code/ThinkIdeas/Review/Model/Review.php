<?php

namespace ThinkIdeas\Review\Model;

use ThinkIdeas\Review\Api\Data\ReviewInterface;

class Review extends \Magento\Framework\Model\AbstractModel implements ReviewInterface
{ 
    /**
     * Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * Aion Test cache tag
     */
    const CACHE_TAG = 'review';

    /**
     * @var string
     */
    protected $_cacheTag = 'review';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'review';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ThinkIdeas\Review\Model\ResourceModel\Review');
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId(), self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get Review ID
     *
     * @return int
     */
    public function getReviewId()
    {
        return parent::getData(self::REVIEWID);
    }

    /**
     * Get Customer Id
     *
     * @return int
     */
    public function getCustomerId()
    {
        return parent::getData(self::CUSTOMERID);
    }

    /**
     * Get Order Id
     *
     * @return int|null
     */
    public function getOrderId()
    {
        return parent::getData(self::ORDERID);
    }

    /**
     * Get Product Ids
     *
     * @return string|null
     */
    public function getProductIds()
    {
        return parent::getData(self::PRODUCTIDS);
    }

    /**
     * Get Email Sent TIme
     *
     * @return string|null
     */
    public function getSentEmailTime()
    {
        return parent::getData(self::SENTEMALTIME);
    }

    /**
     * Get Email Link Status
     *
     * @return int|null
     */
    public function getStatus()
    {
        return parent::getData(self::STATUS);  
    }

    /**
     * Set ID
     *
     * @param int $bannerId
     * @return ReviewInterface
     */
    public function setReviewId($reviewId)
    {
        return $this->setData(self::REVIEWID, $reviewId);
    }

    /**
     * Set Customer Id
     *
     * @param int $customerId
     * @return ReviewInterface
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMERID, $customerId);
    }

    /**
     * Set Order Id
     *
     * @param int $orderId
     * @return ReviewInterface
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDERID, $validfrom);
    }

    /**
     * Set Product Ids
     *
     * @param string $productIds
     * @return ReviewInterface
     */
    public function setProductIds($productIds)
    {
        return $this->setData(self::PRODUCTIDS, $productIds);
    }

    /**
     * Set Email sent Time
     *
     * @param string $sentEmailTime
     * @return ReviewInterface
     */
    public function setSentEmailTime($sentEmailTime)
    {
        return $this->setData(self::SENTEMALTIME, $sentEmailTime);
    }

    /**
     * Set Status
     *
     * @param int $status
     * @return ReviewInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Prepare item's statuses
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

}