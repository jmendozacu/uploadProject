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
 * Class Delete
 * @package ThinkIdeas\Faq\Controller\Adminhtml\Faq
 */
class Delete extends \Magento\Backend\App\Action
{

    /**
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context
    )
    {
        parent::__construct($context);
    }

    /**
     * @return $this
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $faqModel = $this->_objectManager->create('ThinkIdeas\Faq\Model\Faq')
                    ->load($this->getRequest()->getParam('id'));
                $faqModel->deleteAllUrlKey();
                $faqModel->delete();
                $this->messageManager->addSuccess(__('FAQ was successfully deleted'));

            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['_current' => true]);
            }
        }
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
