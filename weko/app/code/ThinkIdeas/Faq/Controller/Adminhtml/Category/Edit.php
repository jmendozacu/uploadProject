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
 * Class Edit
 * @package ThinkIdeas\Faq\Controller\Adminhtml\Category
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
        $resultRedirect = $this->resultRedirectFactory->create();
        $storeViewId = $this->getRequest()->getParam('store');
        $model = $this->_objectManager->create('ThinkIdeas\Faq\Model\Category');
        $registryObject = $this->_objectManager->get('Magento\Framework\Registry');
        if ($id) {
            $model->setStoreViewId($storeViewId)->load($id);
            $model->load($id);
            if (!$model->getCategoryId()) {
                $this->messageManager->addError(__('This category no longer exists.'));
                return $resultRedirect->setPath('faqadmin/*/', ['_current' => true]);
            }
        }
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $registryObject->register('current_category', $model);
        $resultPage = $this->_resultPageFactory->create();
        if (!$model->getCategoryId()) {
            $pageTitle = __('New Category');
        } else {
            $pageTitle =  __('Edit %1', $model->getName());
        }
        $resultPage->getConfig()->getTitle()->prepend($pageTitle);
        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('ThinkIdeas_Faq::category');
    }
}
