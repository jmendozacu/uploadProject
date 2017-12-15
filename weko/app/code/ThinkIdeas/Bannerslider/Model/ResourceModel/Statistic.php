<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Model\ResourceModel;

/**
 * Class Statistic
 * @package ThinkIdeas\Bannerslider\Model\ResourceModel
 */
class Statistic extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_bannerslider_statistic', 'id');
    }
}
