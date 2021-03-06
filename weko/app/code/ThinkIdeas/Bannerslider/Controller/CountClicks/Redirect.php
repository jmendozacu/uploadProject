<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Controller\CountClicks;

use Magento\Framework\Exception\NoSuchEntityException;
use ThinkIdeas\Bannerslider\Api\SlideRepositoryInterface;
use ThinkIdeas\Bannerslider\Api\StatisticRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use ThinkIdeas\Bannerslider\Model\CustomerStatistic\Manager as CustomerStatisticManager;
use Magento\Framework\App\Action\Context;

/**
 * Class Redirect
 * @package ThinkIdeas\Bannerslider\Controller\CountClicks
 */
class Redirect extends \Magento\Framework\App\Action\Action
{
    /**
     * @var SlideRepositoryInterface
     */
    private $slideRepository;

    /**
     * @var StatisticRepositoryInterface
     */
    private $statisticRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var CustomerStatisticManager
     */
    private $customerStatisticManager;

    /**
     * @param Context $context
     * @param SlideRepositoryInterface $slideRepository
     * @param StatisticRepositoryInterface $statisticRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CustomerStatisticManager $customerStatisticManager
     */
    public function __construct(
        Context $context,
        SlideRepositoryInterface $slideRepository,
        StatisticRepositoryInterface $statisticRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CustomerStatisticManager $customerStatisticManager
    ) {
        parent::__construct($context);
        $this->slideRepository = $slideRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->statisticRepository = $statisticRepository;
        $this->customerStatisticManager = $customerStatisticManager;
    }

    /**
     * Update clicks statistics and redirect to url
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $slideId = $this->getRequest()->getParam('slide_id');
        $bannerId = $this->getRequest()->getParam('banner_id');
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($slideId && $bannerId) {
            $this->searchCriteriaBuilder
                ->addFilter('banner_id', $bannerId)
                ->addFilter('slide_id', $slideId);
            $statisticList = $this->statisticRepository
                ->getList($this->searchCriteriaBuilder->create())
                ->getItems();
            foreach ($statisticList as $statistic) {
                $name = 'slide_click_' . $statistic->getSlideBannerId();
                if (!$this->customerStatisticManager->isSetSlideAction($name)) {
                    $statistic->setClickCount((int)$statistic->getClickCount() + 1);
                    $this->statisticRepository->save($statistic);
                    $this->customerStatisticManager->addSlideAction($name);
                }
            }
            $this->customerStatisticManager->save();
            try {
                $slide = $this->slideRepository->get($slideId);
                if ($slide->getUrl()) {
                    return $resultRedirect->setUrl($slide->getUrl());
                }
            } catch (NoSuchEntityException $noEntityException) {
            }
        }
        return $resultRedirect->setRefererOrBaseUrl();
    }
}
