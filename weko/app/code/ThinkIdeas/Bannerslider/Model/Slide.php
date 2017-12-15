<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Model;

use ThinkIdeas\Bannerslider\Model\ResourceModel\Slide as ResourceSlide;

/**
 * Class Slide
 * @package ThinkIdeas\Bannerslider\Model
 */
class Slide extends \Magento\Framework\Model\AbstractModel
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceSlide::class);
    }
}
