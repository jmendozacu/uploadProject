<?php
/**
 * ThinkIdeas
 *
 * @category    ThinkIdeas
 * @package     ThinkIdeas_Faq
 *
 */
namespace ThinkIdeas\Faq\Controller\Adminhtml\Category;

use Magento\Store\Model\Store;

/**
 * Class Save
 * @package ThinkIdeas\Faq\Controller\Adminhtml\Category
 */
class Save extends \Magento\Backend\App\Action
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
        $categoryId = (int)$this->getRequest()->getParam('category_id');
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }
        $storeViewId = $this->getRequest()->getParam("store");

        if ($categoryId) {
            $model = $this->_objectManager->create('ThinkIdeas\Faq\Model\Category')
                ->load($categoryId);
        } else {
            $model = $this->_objectManager->create('ThinkIdeas\Faq\Model\Category');
        }

        $model->setData($data)->setStoreViewId($storeViewId);
        try {
            $model->save();
            $this->messageManager->addSuccess(__('Category was successfully saved'));
        }catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            return  $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        if ($this->getRequest()->getParam('back') == 'edit') {
            return  $resultRedirect->setPath('*/*/edit', ['id' =>$model->getCategoryId()]);
        }
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
