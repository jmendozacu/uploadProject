<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;
use ThinkIdeas\Bannerslider\Api\Data\BlockInterface;

/**
 * Interface for Bannerslider block search results
 *
 * @api
 */
interface BlockSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get blocks list
     *
     * @return BlockInterface[]
     */
    public function getItems();

    /**
     * Set blocks list
     *
     * @param BlockInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
