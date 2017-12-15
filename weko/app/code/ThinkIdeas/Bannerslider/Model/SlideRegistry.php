<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Model;

use ThinkIdeas\Bannerslider\Api\Data\SlideInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use ThinkIdeas\Bannerslider\Api\Data\SlideInterfaceFactory;
use Magento\Framework\EntityManager\EntityManager;

/**
 * Class SlideRegistry
 * @package ThinkIdeas\Bannerslider\Model
 */
class SlideRegistry
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var SlideInterfaceFactory
     */
    private $slideDataFactory;

    /**
     * @var array
     */
    private $slideRegistry = [];

    /**
     * @param EntityManager $entityManager,
     * @param SlideInterfaceFactory $slideDataFactory
     */
    public function __construct(
        EntityManager $entityManager,
        SlideInterfaceFactory $slideDataFactory
    ) {
        $this->entityManager = $entityManager;
        $this->slideDataFactory = $slideDataFactory;
    }

    /**
     * Retrieve Slide from registry by ID
     *
     * @param int $slideId
     * @return Slide
     * @throws NoSuchEntityException
     */
    public function retrieve($slideId)
    {
        if (!isset($this->slideRegistry[$slideId])) {
            /** @var SlideInterface $slide */
            $slide = $this->slideDataFactory->create();
            $this->entityManager->load($slide, $slideId);
            if (!$slide->getId()) {
                throw NoSuchEntityException::singleField('slideId', $slideId);
            } else {
                $this->slideRegistry[$slideId] = $slide;
            }
        }
        return $this->slideRegistry[$slideId];
    }

    /**
     * Remove instance of the Slide from registry by ID
     *
     * @param int $slideId
     * @return void
     */
    public function remove($slideId)
    {
        if (isset($this->slideRegistry[$slideId])) {
            unset($this->slideRegistry[$slideId]);
        }
    }

    /**
     * Replace existing Slide with a new one
     *
     * @param SlideInterface $slide
     * @return $this
     */
    public function push(SlideInterface $slide)
    {
        $this->slideRegistry[$slide->getId()] = $slide;
        return $this;
    }
}
