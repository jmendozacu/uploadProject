<?php
/**
 * Copyright © 2016 AionNext Ltd. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ThinkIdeas\Review\Model\ResourceModel;

/**
 * Aion Test resource model
 */
class Review extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('thinkideas_review_order', 'review_id');
    }
          
}