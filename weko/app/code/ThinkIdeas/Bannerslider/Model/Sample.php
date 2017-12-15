<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Model;

/**
 * class Sample
 * @package ThinkIdeas\Bannerslider\Model
 */
class Sample extends \Magento\Framework\Config\Data
{
    /**
     * @param \ThinkIdeas\Bannerslider\Model\Sample\Reader\Xml $reader
     * @param \Magento\Framework\Config\CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        \ThinkIdeas\Bannerslider\Model\Sample\Reader\Xml $reader,
        \Magento\Framework\Config\CacheInterface $cache,
        $cacheId = 'thinkIdeas_bannerslider_sample_data_cache'
    ) {
        parent::__construct($reader, $cache, $cacheId);
    }
}
