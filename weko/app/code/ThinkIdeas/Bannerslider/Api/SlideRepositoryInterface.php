<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Slide CRUD interface.
 * @api
 */
interface SlideRepositoryInterface
{
    /**
     * Save slide.
     *
     * @param \ThinkIdeas\Bannerslider\Api\Data\SlideInterface $slide
     * @return \ThinkIdeas\Bannerslider\Api\Data\SlideInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\ThinkIdeas\Bannerslider\Api\Data\SlideInterface $slide);

    /**
     * Retrieve slide.
     *
     * @param int $slideId
     * @return \ThinkIdeas\Bannerslider\Api\Data\SlideInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($slideId);

    /**
     * Retrieve slides matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \ThinkIdeas\Bannerslider\Api\Data\SlideSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete slide.
     *
     * @param \ThinkIdeas\Bannerslider\Api\Data\SlideInterface $slide
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\ThinkIdeas\Bannerslider\Api\Data\SlideInterface $slide);

    /**
     * Delete slide by ID.
     *
     * @param int $slideId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($slideId);
}
