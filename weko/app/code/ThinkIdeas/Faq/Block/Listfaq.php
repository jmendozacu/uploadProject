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
 * Class Listfaq
 * @package ThinkIdeas\Faq\Block
 */
class Listfaq extends \Magento\Framework\View\Element\Template
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
     * @var \ThinkIdeas\Faq\Model\FaqFactory
     */
    protected $_faqFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \ThinkIdeas\Faq\Helper\Data $faqHelper
     * @param \ThinkIdeas\Faq\Model\ResourceModel\Faq\CollectionFactory $faqCollectionFactory
     * @param \ThinkIdeas\Faq\Model\FaqFactory $faqFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \ThinkIdeas\Faq\Helper\Data $faqHelper,
        \ThinkIdeas\Faq\Model\ResourceModel\Faq\CollectionFactory $faqCollectionFactory,
        \ThinkIdeas\Faq\Model\FaqFactory $faqFactory,
        array $data = [])
    {
        $this->_faqHelper = $faqHelper;
        $this->_faqCollectionFactory = $faqCollectionFactory;
        $this->_faqFactory = $faqFactory;
        parent::__construct($context, $data);
    }

    /**
     *
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }

    /**
     * @return $this
     */
    public function getFaqCollection() {
        $type = $this->getRequest()->getParam('faqType');
        $id = $this->getRequest()->getParam('faqId');
        $page = $this->getRequest()->getParam('page');
        $store_Id = $this->_faqHelper->getStoreId();
        $page_size = $this->_faqHelper->getPageSizeNumber();
        if (!$page_size)
            $page_size = 20;
        $faqId = $this->getRequest()->getParam('id');
        if ($type == 'category') {
            $collection = $this->getCategoryFaq($id);
        } elseif ($type == 'all') {
            $collection = $this->getAllFaq();
        } elseif ($type == 'tag') {
            $collection = $this->getTagFaq($id);
        } elseif ($type == 'search') {
            $collection = $this->getSearchResult($id);
        } elseif (!$type && $faqId) {
            $collection = $this->getViewFaq($faqId);
        } else {
            $collection = $this->getMostFrequently();
        }
        if (!$type && $faqId) {

        } else {
            $collection->setPageSize($page_size);
            if ($page)
                $collection->setCurPage($page);
        }
        return $collection;
    }

    /**
     * @return $this
     */
    public function getMostFrequently() {
        $most_frequently = $this->_faqCollectionFactory->create()
            ->setStoreViewId($this->_faqHelper->getStoreId())
            ->addFieldToFilter('most_frequently', 1)
            ->addFieldToFilter('status', 1);
        $most_frequently->getSelect()->order('CAST(ordering AS SIGNED) ASC');
        $most_frequently->setOrder('title', 'ASC')
            ->setOrder('update_time', 'DESC');
        return $most_frequently;
    }

    /**
     * @return $this
     */
    public function getAllFaq() {
        $all = $this->_faqCollectionFactory->create()
            ->setStoreViewId($this->_faqHelper->getStoreId())
            ->addFieldToFilter('status', 1);
        $all->getSelect()->order('CAST(ordering AS SIGNED) ASC');
        $all->setOrder('title', 'ASC')
            ->setOrder('update_time', 'DESC');
        return $all;
    }

    /**
     * @param $category_id
     * @return $this
     */
    public function getCategoryFaq($category_id) {
        $category = $this->_faqCollectionFactory->create()
            ->setStoreViewId($this->_faqHelper->getStoreId())
            ->addFieldToFilter('category_id', $category_id)
            ->addFieldToFilter('status', 1);
        $category->getSelect()->order('CAST(ordering AS SIGNED) ASC');
        $category->setOrder('title', 'ASC')
            ->setOrder('update_time', 'DESC');
        return $category;
    }

    /**
     * @param $keyword
     * @return $this
     */
    public function getSearchResult($keyword) {
        $keyword = addslashes($keyword);
        $result =$this->_faqCollectionFactory->create()
            ->setStoreViewId($this->_faqHelper->getStoreId())
            ->addFieldToFilter('status', 1)
            ->filterResult($keyword);

        $result->getSelect()->order('CAST(ordering AS SIGNED) ASC');
        $result->setOrder('title', 'ASC')
            ->setOrder('update_time', 'DESC');

        return $result;
    }

    /**
     * @param $tag
     * @return $this
     */
    public function getTagFaq($tag) {
        $faq =$this->_faqCollectionFactory->create()
            ->setStoreViewId($this->_faqHelper->getStoreId())
            ->addFieldToFilter('tag', array('like' => '%' . $tag . '%'))
            ->addFieldToFilter('status', 1);
        $faq->getSelect()->order('CAST(ordering AS SIGNED) ASC');
        $faq->setOrder('title', 'ASC')
            ->setOrder('update_time', 'DESC');
        return $faq;
    }

    /**
     * @param $id
     * @return $this
     */
    public function getViewFaq($id) {
        $faq =$this->_faqFactory->create()->setStoreViewId($this->_faqHelper->getStoreId())->load($id);
        return $faq;
    }
}