<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Api;

use ThinkIdeas\Bannerslider\Api\Data\BlockSearchResultsInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Bannerslider block repository interface
 *
 * @api
 */
interface BlockRepositoryInterface
{
    /**
     * Retrieve block(s) matching the specified blockType and blockPosition
     * Update views block statistics if necessary
     *
     * @param int $blockType
     * @param int $blockPosition
     * @param bool $allBlocks
     * @param bool $updateViewsStatistic
     *
     * @return BlockSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList($blockType, $blockPosition, $allBlocks = false, $updateViewsStatistic = true);
}
