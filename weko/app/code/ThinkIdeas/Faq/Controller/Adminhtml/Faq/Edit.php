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
 * Class Edit
 * @package ThinkIdeas\Faq\Controller\Adminhtml\Faq
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
    }

    /**
     * @return $this|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {

        $id = $this->getRequest()->getParam('id');
        $storeViewId = $this->getRequest()->getParam('store');
        $model = $this->_objectManager->create('ThinkIdeas\Faq\Model\Faq');
        $resultRedirect = $this->resultRedirectFactory->create();
        $registryObject = $this->_objectManager->get('Magento\Framework\Registry');
        if ($id) {
            $model->setStoreViewId($storeViewId)->load($id);
            if (!$model->getFaqId()) {
                $this->messageManager->addError(__('This Faq no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
        }
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $registryObject->register('current_faq', $model);
        $resultPage = $this->_resultPageFactory->create();
        if (!$model->getFaqId()) {
            $pageTitle = __('New FAQ');
        } else {
            $pageTitle =  __('Edit %1', $model->getTitle());
        }
        $resultPage->getConfig()->getTitle()->prepend($pageTitle);
        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('ThinkIdeas_Faq::faq_faq');
    }
}
