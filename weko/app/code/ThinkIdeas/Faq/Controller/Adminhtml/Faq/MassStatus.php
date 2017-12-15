<?php
/**
 * ThinkIdeas
 *
 * @category    ThinkIdeas
 * @package     ThinkIdeas_Faq
 *
 */
namespace ThinkIdeas\Faq\Controller\Adminhtml\Faq;
/**
 * Class MassStatus
 * @package ThinkIdeas\Faq\Controller\Adminhtml\Faq
 */
class MassStatus extends \Magento\Backend\App\Action
{
    /**
     * @return $this
     */
    public function execute()
    {
        $faqIds = $this->getRequest()->getParam('faq');
        $status = $this->getRequest()->getParam('status');
        $storeViewId = $this->getRequest()->getParam('store');

        if (!is_array($faqIds) || empty($faqIds)) {
            $this->messageManager->addError(__('Please select faq(s).'));
        } else {
            try {
                foreach ($faqIds as $faqId) {
                    $faq = $this->_objectManager->create('ThinkIdeas\Faq\Model\Faq')
                        ->setStoreViewId($storeViewId)
                        ->load($faqId);
                    $faq->setStatus($status)
                           ->setIsMassupdate(true)
                           ->save();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been changed status.', count($faqIds))
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
        return $this->_authorization->isAllowed('ThinkIdeas_Faq::faq_faq');
    }
}
