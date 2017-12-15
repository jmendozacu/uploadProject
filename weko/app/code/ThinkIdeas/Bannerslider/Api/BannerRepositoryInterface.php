<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Banner CRUD interface
 * @api
 */
interface BannerRepositoryInterface
{
    /**
     * Save banner
     *
     * @param \ThinkIdeas\Bannerslider\Api\Data\BannerInterface $banner
     * @return \ThinkIdeas\Bannerslider\Api\Data\BannerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\ThinkIdeas\Bannerslider\Api\Data\BannerInterface $banner);

    /**
     * Retrieve banner
     *
     * @param int $bannerId
     * @return \ThinkIdeas\Bannerslider\Api\Data\BannerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($bannerId);

    /**
     * Retrieve banners matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \ThinkIdeas\Bannerslider\Api\Data\BannerSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete banner
     *
     * @param \ThinkIdeas\Bannerslider\Api\Data\BannerInterface $banner
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\ThinkIdeas\Bannerslider\Api\Data\BannerInterface $banner);

    /**
     * Delete banner by ID
     *
     * @param int $bannerId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($bannerId);
}
