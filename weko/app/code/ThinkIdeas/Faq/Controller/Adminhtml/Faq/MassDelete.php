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
 * Class MassDelete
 * @package ThinkIdeas\Faq\Controller\Adminhtml\Faq
 */
class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @return $this
     */
    public function execute()
    {
        $faqIds = $this->getRequest()->getParam('faq');
        if (!is_array($faqIds) || empty($faqIds)) {
            $this->messageManager->addError(__('Please select faq(s).'));
        } else {
            try {
                foreach ($faqIds as $faqId) {
                    $faq = $this->_objectManager->create('ThinkIdeas\Faq\Model\Faq')
                        ->load($faqId);
                    $faq->deleteAllUrlKey();
                    $faq->delete();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been deleted.', count($faqIds))
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
