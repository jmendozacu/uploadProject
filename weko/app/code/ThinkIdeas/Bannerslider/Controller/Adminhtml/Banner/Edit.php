<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Controller\Adminhtml\Banner;

use Magento\Framework\Exception\NoSuchEntityException;
use ThinkIdeas\Bannerslider\Api\BannerRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Edit
 * @package ThinkIdeas\Bannerslider\Controller\Adminhtml\Banner
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'ThinkIdeas_Bannerslider::banners';

    /**
     * @var BannerRepositoryInterface
     */
    private $bannerRepository;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param BannerRepositoryInterface $bannerRepository
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        BannerRepositoryInterface $bannerRepository,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->bannerRepository = $bannerRepository;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Edit Banner
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->bannerRepository->get($id);
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Something went wrong while editing the banner')
                );
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/*/index');
                return $resultRedirect;
            }
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage
            ->setActiveMenu('ThinkIdeas_Bannerslider::banners')
            ->getConfig()->getTitle()->prepend(
                $id ? __('Edit Banner') : __('New Banner')
            );

        return $resultPage;
    }
}
