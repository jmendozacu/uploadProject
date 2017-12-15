<?php
/**
* Copyright 2016 thinkIdeas. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace ThinkIdeas\Bannerslider\Controller\Adminhtml\Slide;

use Magento\Backend\App\Action\Context;
use ThinkIdeas\Bannerslider\Api\SlideRepositoryInterface;

/**
 * Class Delete
 * @package ThinkIdeas\Bannerslider\Controller\Adminhtml\Slide
 */
class Delete extends \Magento\Backend\App\Action
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
     * @param Context $context
     * @param SlideRepositoryInterface $slideRepository
     */
    public function __construct(
        Context $context,
        SlideRepositoryInterface $slideRepository
    ) {
        parent::__construct($context);
        $this->slideRepository = $slideRepository;
    }

    /**
     * Delete slide action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = (int)$this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->slideRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('Slide was successfully deleted'));
                return $resultRedirect->setPath('*/*/index');
            } catch (\Exception $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            }
        }
        $this->messageManager->addErrorMessage(__('Slide could not be deleted'));
        return $resultRedirect->setPath('*/*/index');
    }
}
