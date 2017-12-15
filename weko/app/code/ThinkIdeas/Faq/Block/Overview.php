<?php
/**
 * ThinkIdeas
 *
 * @category    ThinkIdeas
 * @package     ThinkIdeas_Faq
 *
 */
namespace ThinkIdeas\Faq\Block;
/**
 * Class Overview
 * @package ThinkIdeas\Faq\Block
 */
class Overview extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \ThinkIdeas\Faq\Helper\Data
     */
    protected $_faqHelper;
    /**
     * @var \ThinkIdeas\Faq\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_categoryCollectionFactory;
    /**
     * @var \ThinkIdeas\Faq\Model\FaqFactory
     */
    protected $_faqFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \ThinkIdeas\Faq\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \ThinkIdeas\Faq\Model\FaqFactory $faqFactory
     * @param \ThinkIdeas\Faq\Helper\Data $faqHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \ThinkIdeas\Faq\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \ThinkIdeas\Faq\Model\FaqFactory $faqFactory,
        \ThinkIdeas\Faq\Helper\Data $faqHelper,
        array $data = [])
    {
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_faqFactory = $faqFactory;
        $this->_faqHelper = $faqHelper;
        parent::__construct($context, $data);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _prepareLayout() {
        parent::_prepareLayout();
        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            );
            $breadcrumbsBlock->addCrumb(
                'faq',
                [
                    'label' => __('FAQ'),
                    'title' => __('FAQ'),
                    'link' => $this->_storeManager->getStore()->getUrl("faq")
                ]
            );
        }
        $storeId = $this->_storeManager->getStore(true)->getStoreId();
        $faqId =  $this->getRequest()->getParam('id');
        $meta_keywords = $this->_faqHelper->getMetaKeywords();
        $meta_description = $this->_faqHelper->getMetaDescription();
        if($faqId){
            $faq = $this->_faqFactory->create()
                ->setStoreViewId($storeId)->load($faqId);
            $meta_keywords = $faq->getMetakeyword();
            $meta_description = $faq->getMetadescription();
        }
        $this->pageConfig->setDescription($meta_description);
        $this->pageConfig->setKeywords($meta_keywords);
    }

    /**
     * @return $this
     */
    public function getAllCategory() {
        $storeId = $this->_storeManager->getStore(true)->getStoreId();
        $categories =  $this->_categoryCollectionFactory->create()
            ->setStoreViewId($storeId)
            ->addFieldToFilter('status',1);
        $categories->getSelect()->order('CAST(ordering AS SIGNED) ASC');
        $categories ->setOrder('name', 'ASC');
        return $categories;
    }

}