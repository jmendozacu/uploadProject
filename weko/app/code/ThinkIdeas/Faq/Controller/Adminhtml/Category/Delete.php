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
 * Class Delete
 * @package ThinkIdeas\Faq\Controller\Adminhtml\Category
 */
class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var \ThinkIdeas\Faq\Model\CategoryFactory
     */
    protected $_categoryFactory;
    /**
     * @var \ThinkIdeas\Faq\Model\ResourceModel\Faq\CollectionFactory
     */
    protected $_faqCollectionFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \ThinkIdeas\Faq\Model\CategoryFactory $categoryFactory
     * @param \ThinkIdeas\Faq\Model\ResourceModel\Faq\CollectionFactory $faqCollectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \ThinkIdeas\Faq\Model\CategoryFactory $categoryFactory,
        \ThinkIdeas\Faq\Model\ResourceModel\Faq\CollectionFactory $faqCollectionFactory
    )
    {
        parent::__construct($context);
        $this->_categoryFactory = $categoryFactory;
        $this->_faqCollectionFactory = $faqCollectionFactory;
    }

    /**
     * @return $this
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $categoryId = $this->getRequest()->getParam('id');
        if ($categoryId > 0) {
            try {
                $categoryModel = $this->_categoryFactory->create()->load($this->getRequest()->getParam('id'));
                $questions =  $this->_faqCollectionFactory->create()->addFieldToFilter('category_id', $categoryId);
                if (!$questions->getsize()) {
                    $categoryModel->delete();
                    $this->messageManager->addSuccess(__('Category was successfully deleted'));
                } else {
                    $this->messageManager->addError(__('Cannot delete Category that contained FAQs'));
                }

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
        return $this->_authorization->isAllowed('ThinkIdeas_Faq::category');
    }

}
