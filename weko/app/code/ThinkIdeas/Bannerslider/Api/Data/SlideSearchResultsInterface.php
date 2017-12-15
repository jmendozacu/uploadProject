<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for slide search results.
 * @api
 */
interface SlideSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get slides list.
     *
     * @return \ThinkIdeas\Bannerslider\Api\Data\SlideInterface[]
     */
    public function getItems();

    /**
     * Set slides list.
     *
     * @param \ThinkIdeas\Bannerslider\Api\Data\SlideInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
