<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Model\ResourceModel;

use ThinkIdeas\Bannerslider\Api\Data\SlideInterface;
use ThinkIdeas\Bannerslider\Api\Data\SlideInterfaceFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use ThinkIdeas\Bannerslider\Model\SlideRegistry;
use ThinkIdeas\Bannerslider\Api\Data\SlideSearchResultsInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use ThinkIdeas\Bannerslider\Api\Data\SlideSearchResultsInterface;
use ThinkIdeas\Bannerslider\Model\Slide as SlideModel;
use Magento\Framework\EntityManager\EntityManager;
use ThinkIdeas\Bannerslider\Model\SlideFactory;

/**
 * Class SlideRepository
 * @package ThinkIdeas\Bannerslider\Model\ResourceModel
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SlideRepository implements \ThinkIdeas\Bannerslider\Api\SlideRepositoryInterface
{
    /**
     * @var SlideFactory
     */
    private $slideFactory;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var SlideInterfaceFactory
     */
    private $slideDataFactory;

    /**
     * @var SlideRegistry
     */
    private $slideRegistry;

    /**
     * @var SlideSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * @param SlideFactory $slideFactory
     * @param EntityManager $entityManager
     * @param SlideInterfaceFactory $slideDataFactory
     * @param SlideRegistry $slideRegistry
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param SlideSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     */
    public function __construct(
        SlideFactory $slideFactory,
        EntityManager $entityManager,
        SlideInterfaceFactory $slideDataFactory,
        SlideRegistry $slideRegistry,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        SlideSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor
    ) {
        $this->slideFactory = $slideFactory;
        $this->entityManager = $entityManager;
        $this->slideDataFactory = $slideDataFactory;
        $this->slideRegistry = $slideRegistry;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function save(SlideInterface $slide)
    {
        $slide = $this->entityManager->save($slide);
        $this->slideRegistry->push($slide);
        return $slide;
    }

    /**
     * {@inheritdoc}
     */
    public function get($slideId)
    {
        return $this->slideRegistry->retrieve($slideId);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var SlideSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create()
            ->setSearchCriteria($searchCriteria);
        /** @var \ThinkIdeas\Bannerslider\Model\ResourceModel\Slide\Collection $collection */
        $collection = $this->slideFactory->create()->getCollection();
        $this->extensionAttributesJoinProcessor->process($collection, SlideInterface::class);
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() == SlideInterface::STORE_IDS) {
                    $collection->addStoreFilter($filter->getValue());
                } elseif ($filter->getField() == SlideInterface::CUSTOMER_GROUP_IDS) {
                    $collection->addCustomerGroupFilter($filter->getValue());
                } elseif ($filter->getField() == 'date') {
                    $collection->addDateFilter($filter->getValue());
                } elseif ($filter->getField() == 'banner_id') {
                    $collection->addBannerFilter($filter->getValue());
                } else {
                    $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                    $fields[] = $filter->getField();
                    $conditions[] = [$condition => $filter->getValue()];
                }
            }
            if ($fields) {
                $collection->addFieldToFilter($fields, $conditions);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        if ($sortOrders = $searchCriteria->getSortOrders()) {
            /** @var \Magento\Framework\Api\SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder($sortOrder->getField(), $sortOrder->getDirection());
            }
        }

        $collection
            ->setCurPage($searchCriteria->getCurrentPage())
            ->setPageSize($searchCriteria->getPageSize());

        $slides = [];
        /** @var SlideModel $slideModel */
        foreach ($collection as $slideModel) {
            $slides[] = $this->getSlideDataObject($slideModel);
        }
        $searchResults->setItems($slides);
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(SlideInterface $slide)
    {
        return $this->deleteById($slide->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($slideId)
    {
        $slide = $this->slideRegistry->retrieve($slideId);
        $this->entityManager->delete($slide);
        $this->slideRegistry->remove($slideId);
        return true;
    }

    /**
     * Retrieves slide data object using Slide Model
     *
     * @param SlideModel $slide
     * @return SlideInterface
     */
    private function getSlideDataObject(SlideModel $slide)
    {
        /** @var SlideInterface $slideDataObject */
        $slideDataObject = $this->slideDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $slideDataObject,
            $slide->getData(),
            SlideInterface::class
        );
        return $slideDataObject;
    }
}
