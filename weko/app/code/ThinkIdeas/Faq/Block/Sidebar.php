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
 * Class Sidebar
 * @package ThinkIdeas\Faq\Block
 */
class Sidebar extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \ThinkIdeas\Faq\Helper\Data
     */
    protected $_faqHelper;
    /**
     * @var \ThinkIdeas\Faq\Model\ResourceModel\Faq\CollectionFactory
     */
    protected $_faqCollectionFactory;
    /**
     * @var \ThinkIdeas\Faq\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_categoryCollectionFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \ThinkIdeas\Faq\Helper\Data $helperData
     * @param \ThinkIdeas\Faq\Model\ResourceModel\Faq\CollectionFactory $faqCollectionFactory
     * @param \ThinkIdeas\Faq\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \ThinkIdeas\Faq\Helper\Data $helperData,
        \ThinkIdeas\Faq\Model\ResourceModel\Faq\CollectionFactory $faqCollectionFactory,
        \ThinkIdeas\Faq\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        array $data = [])
    {
        $this->_faqHelper = $helperData;
        $this->_faqCollectionFactory = $faqCollectionFactory;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getSidebarPosition() {
        $sidebarPosition = $this->_faqHelper->getSidebarPosition();
        return $sidebarPosition;
    }

    /**
     * @return $this
     */
    public function getMostFrequently()
    {
        $page_size =$this->_faqHelper->getSidebarQuestionNumber();

        if($page_size==0) $page_size =5;
        $most_frequently = $this->_faqCollectionFactory->create()
            ->setStoreViewId($this->_faqHelper->getStoreId())
            ->addFieldToFilter('most_frequently',1)
            ->addFieldToFilter('status',1)
            ->setOrder('ordering', 'ASC')
            ->setOrder('title', 'ASC')
            ->setOrder('update_time', 'DESC');
        $most_frequently->setPageSize($page_size);
        return $most_frequently;
    }

    /**
     * @return string
     */
    public function getFaqUrl()
    {
        return $this->getUrl('faq/index/index');
    }

}