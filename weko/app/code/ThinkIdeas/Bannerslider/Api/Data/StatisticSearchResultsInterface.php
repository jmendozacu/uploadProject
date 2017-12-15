<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for statistic search results.
 * @api
 */
interface StatisticSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get banners list.
     *
     * @return \ThinkIdeas\Bannerslider\Api\Data\StatisticInterface[]
     */
    public function getItems();

    /**
     * Set banners list.
     *
     * @param \ThinkIdeas\Bannerslider\Api\Data\StatisticInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
