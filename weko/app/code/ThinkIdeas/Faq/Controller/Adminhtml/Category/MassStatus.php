<?php
/**
 * ThinkIdeas
 *
 * @category    ThinkIdeas
 * @package     ThinkIdeas_Faq
 *
 */
namespace ThinkIdeas\Faq\Controller\Adminhtml\Category;
/**
 * Class MassStatus
 * @package ThinkIdeas\Faq\Controller\Adminhtml\Category
 */
class MassStatus extends \Magento\Backend\App\Action
{
    /**
     * @return $this
     */
    public function execute()
    {
        $categoryIds = $this->getRequest()->getParam('category');
        $status = $this->getRequest()->getParam('status');
        $storeViewId = $this->getRequest()->getParam('store');

        if (!is_array($categoryIds) || empty($categoryIds)) {
            $this->messageManager->addError(__('Please select banner(s).'));
        } else {
            try {
                foreach ($categoryIds as $categoryId) {
                    $category = $this->_objectManager->create('ThinkIdeas\Faq\Model\Category')
                        ->setStoreViewId($storeViewId)
                        ->load($categoryId);
                    $category->setStatus($status)
                           ->setIsMassupdate(true)
                           ->save();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been changed status.', count($categoryIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('ThinkIdeas_Faq::category');
    }
}
