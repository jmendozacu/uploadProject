<?php
declare(strict_types=1);
/**
 * ThinkIdeas_Storelocator extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  ThinkIdeas
 * @package   ThinkIdeas_Storelocator
 * @copyright 2016 Claudiu Creanga
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Claudiu Creanga
 */
namespace ThinkIdeas\Storelocator\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use ThinkIdeas\Storelocator\Api\Data\StockistInterface;

/**
 * @api
 */
interface StockistRepositoryInterface
{
    /**
     * Save page.
     *
     * @param StockistInterface $stockist
     * @return StockistInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(StockistInterface $stockist);

    /**
     * Retrieve Stockist.
     *
     * @param int $stockistId
     * @return StockistInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($stockistId);

    /**
     * Retrieve pages matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \ThinkIdeas\Storelocator\Api\Data\StockistSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);


    /**
     * Retrieve Product Ids matching the specified criteria.
     *
     * @param \ThinkIdeas\Storelocator\Api\StockistRepositoryInterface $stockistRepository
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProducts(\ThinkIdeas\Storelocator\Api\StockistRepositoryInterface $stockistRepository);

    /**
     * Delete stockist.
     *
     * @param StockistInterface $stockist
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(StockistInterface $stockist);

    /**
     * Delete stockist by ID.
     *
     * @param int $stockistId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($stockistId);
}
