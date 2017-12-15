<?php
/**
 * ThinkIdeas
 *
 * @category    ThinkIdeas
 * @package     ThinkIdeas_Faq
 *
 */
namespace ThinkIdeas\Faq\Controller\Index;
/**
 * Class Index
 * @package ThinkIdeas\Faq\Controller\Index
 */
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var
     */
    protected $storeManager;
    /**
     * @var
     */
    protected $_registry;
    /**
     * @var
     */
    protected $_categoryFactory;
    /**
     * @var \ThinkIdeas\Faq\Helper\Data
     */
    protected $_faqHelper;
    /**
     * @var
     */
    protected $_faqFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \ThinkIdeas\Faq\Helper\Data $helperData
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \ThinkIdeas\Faq\Helper\Data $helperData
    )
    {
        parent::__construct($context);
        $this->_faqHelper = $helperData;
    }

    /**
     * @return $this|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $faqId =  $this->getRequest()->getParam('id');
        $status = 1;
        if ($faqId) {
            $faqModel = $this->_objectManager->create('ThinkIdeas\Faq\Model\Faq')->load($faqId);
            if ($faqModel) {
                if ($faqModel->getFaqId()) {
                    $status = $faqModel->getStatus();
                }
            }
        }
        if( $this->_faqHelper->isEnableConfig() && $status==1) {
            $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
            return $resultPage;
        } else {
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('cms/index/noRoute');
        }
    }
}
