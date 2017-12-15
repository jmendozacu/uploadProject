<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ThinkIdeas\Review\Model\Config\Backend\OrderReview\Link;

/**
 * Customer Reset Password Link Expiration period backend model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Expirationperiod extends \Magento\Framework\App\Config\Value
{
    /**
     * Validate expiration period value before saving
     *
     * @return $this
     */
    public function beforeSave()
    {
        parent::beforeSave();
        $resetOrderReviewLinkExpirationPeriod = (int)$this->getValue();

        if ($resetOrderReviewLinkExpirationPeriod < 1) {
            $resetOrderReviewLinkExpirationPeriod = (int)$this->getOldValue();
        }
        $this->setValue((string)$resetOrderReviewLinkExpirationPeriod);
        return $this;
    }
}
