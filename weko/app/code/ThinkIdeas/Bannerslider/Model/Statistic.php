<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Model;

use ThinkIdeas\Bannerslider\Model\ResourceModel\Statistic as ResourceStatistic;

/**
 * Class Statistic
 * @package ThinkIdeas\Bannerslider\Model
 */
class Statistic extends \Magento\Framework\Model\AbstractModel
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceStatistic::class);
    }
}
