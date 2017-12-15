<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Controller\Adminhtml\Slide;

use Magento\Framework\Exception\NoSuchEntityException;
use ThinkIdeas\Bannerslider\Api\SlideRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Edit
 * @package ThinkIdeas\Bannerslider\Controller\Adminhtml\Slide
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'ThinkIdeas_Bannerslider::slides';

    /**
     * @var SlideRepositoryInterface
     */
    private $slideRepository;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param SlideRepositoryInterface $slideRepository
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        SlideRepositoryInterface $slideRepository,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->slideRepository = $slideRepository;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Edit Slide
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->slideRepository->get($id);
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Something went wrong while editing the slide')
                );
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/*/index');
                return $resultRedirect;
            }
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage
            ->setActiveMenu('ThinkIdeas_Bannerslider::slides')
            ->getConfig()->getTitle()->prepend(
                $id ? __('Edit Slide') : __('New Slide')
            );

        return $resultPage;
    }
}
