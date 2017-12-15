<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for banner search results.
 * @api
 */
interface BannerSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get banners list.
     *
     * @return \ThinkIdeas\Bannerslider\Api\Data\BannerInterface[]
     */
    public function getItems();

    /**
     * Set banners list.
     *
     * @param \ThinkIdeas\Bannerslider\Api\Data\BannerInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
