<?php
/**
 * ThinkIdeas
 *
 * @category    ThinkIdeas
 * @package     ThinkIdeas_Faq
 *
 */
namespace ThinkIdeas\Faq\Controller\Adminhtml\Faq;
use Magento\Store\Model\Store;

/**
 * Class Save
 * @package ThinkIdeas\Faq\Controller\Adminhtml\Faq
 */
class Save extends \Magento\Backend\App\Action
{

    /**
     * @var
     */
    protected $_faqHelper;

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
        $faqId = (int)$this->getRequest()->getParam('faq_id');
        $data = $this->getRequest()->getPostValue();
        if (isset($data['url_key']) && $data['url_key']) {
            $data['url_key'] =$this->_objectManager->create('ThinkIdeas\Faq\Helper\Data')
                ->normalizeUrlKey($data['url_key']);
        } elseif (isset($data['title'])) {
            $data['url_key'] = $this->_objectManager->create('ThinkIdeas\Faq\Helper\Data')
                ->normalizeUrlKey($data['title']);
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }
        $storeViewId = $this->getRequest()->getParam("store");
        if ($faqId) {
            $model = $this->_objectManager->create('ThinkIdeas\Faq\Model\Faq')
                ->load($faqId);
        } else {
            $model = $this->_objectManager->create('ThinkIdeas\Faq\Model\Faq');
        }

        $model->setData($data)->setStoreViewId($storeViewId);
        try {
            $model->save();
            $model->setStoreViewId($storeViewId)->updateUrlKey();
            $this->messageManager->addSuccess(__('FAQ was successfully saved'));
        }catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            return  $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        if ($this->getRequest()->getParam('back') == 'edit') {
            return  $resultRedirect->setPath('*/*/edit', ['id' =>$model->getFaqId()]);
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
