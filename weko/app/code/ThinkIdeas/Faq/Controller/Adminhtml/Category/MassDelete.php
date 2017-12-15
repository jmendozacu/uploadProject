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
 * Class MassDelete
 * @package ThinkIdeas\Faq\Controller\Adminhtml\Category
 */
class MassDelete extends \Magento\Backend\App\Action
{
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
        $categoryIds = $this->getRequest()->getParam('category');
        if (!is_array($categoryIds) || empty($categoryIds)) {
            $this->messageManager->addError(__('Please select category(s).'));
        } else {
            $count = 0;
            try {
                foreach ($categoryIds as $categoryId) {
                    $category = $this->_objectManager->create('ThinkIdeas\Faq\Model\Category')
                        ->load($categoryId);
                    $questions = $this->_faqCollectionFactory->create()
                        ->addFieldToFilter('category_id', $categoryId);
                    if (!$questions->getsize()) {
                        $category->delete();
                        $count++;
                    } else {
                        $this->messageManager->addError(__('Cannot delete Category "%1" that contained FAQs', $category->getName()));
                    }
                }
                if ($count) {
                    $this->messageManager->addSuccess(
                        __('Total of %d record(s) were successfully deleted', $count)
                    );
                }
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
