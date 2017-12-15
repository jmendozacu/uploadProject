<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Statistic CRUD interface.
 * @api
 */
interface StatisticRepositoryInterface
{
    /**
     * Save statistic.
     *
     * @param \ThinkIdeas\Bannerslider\Api\Data\StatisticInterface $statistic
     * @return \ThinkIdeas\Bannerslider\Api\Data\StatisticInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\ThinkIdeas\Bannerslider\Api\Data\StatisticInterface $statistic);

    /**
     * Retrieve statistic.
     *
     * @param int $statisticId
     * @return \ThinkIdeas\Bannerslider\Api\Data\StatisticInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($statisticId);

    /**
     * Retrieve statistics matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \ThinkIdeas\Bannerslider\Api\Data\StatisticSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete statistic.
     *
     * @param \ThinkIdeas\Bannerslider\Api\Data\StatisticInterface $statistic
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\ThinkIdeas\Bannerslider\Api\Data\StatisticInterface $statistic);

    /**
     * Delete statistic by ID.
     *
     * @param int $statisticId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($statisticId);
}
