<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use ThinkIdeas\Bannerslider\Api\Data\BannerInterface;
use ThinkIdeas\Bannerslider\Api\BannerRepositoryInterface;
use ThinkIdeas\Bannerslider\Api\Data\BannerInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use ThinkIdeas\Bannerslider\Model\Sample;

/**
 * class InstallData
 * @package ThinkIdeas\Bannerslider\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var BannerRepositoryInterface
     */
    private $bannerRepository;

    /**
     * @var BannerInterfaceFactory
     */
    private $bannerDataFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var Sample
     */
    private $sampleData;

    /**
     * @param BannerRepositoryInterface $bannerRepository
     * @param BannerInterfaceFactory $bannerDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param Sample $sampleData
     */
    public function __construct(
        BannerRepositoryInterface $bannerRepository,
        BannerInterfaceFactory $bannerDataFactory,
        DataObjectHelper $dataObjectHelper,
        Sample $sampleData
    ) {
        $this->bannerRepository = $bannerRepository;
        $this->bannerDataFactory = $bannerDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->sampleData = $sampleData;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        foreach ($this->sampleData->get() as $data) {
            try {
                $bannerDataObject = $this->bannerDataFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $bannerDataObject,
                    $data,
                    BannerInterface::class
                );

                if (!$bannerDataObject->getId()) {
                    $bannerDataObject->setId(null);
                }

                $this->bannerRepository->save($bannerDataObject);
            } catch (\Exception $e) {
                throw $e;
            }
        }
    }
}
