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
 * Class Tag
 * @package ThinkIdeas\Faq\Block
 */
class Tag extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \ThinkIdeas\Faq\Model\ResourceModel\Faq\CollectionFactory
     */
    protected $_faqCollectionFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \ThinkIdeas\Faq\Model\ResourceModel\Faq\CollectionFactory $faqCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \ThinkIdeas\Faq\Model\ResourceModel\Faq\CollectionFactory $faqCollectionFactory,
        array $data = [])
    {
        $this->_faqCollectionFactory = $faqCollectionFactory;
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
     * @return array
     */
    public function getAllTags(){
        $faqCollection= $this->_faqCollectionFactory->create()
            ->setStoreViewId($this->_storeManager->getStore(true)->getStoreId())
            ->addFieldToFilter('status',1)
            ;
        $tagArray = array();
        foreach ($faqCollection as $faq) {
            $tag = $faq->getTag();
            $tagArrayFaq = explode(',',$tag);
            foreach ($tagArrayFaq as $oneTag) {
                $oneTag = trim($oneTag);
                if ($oneTag && !in_array($oneTag,$tagArray)) {
                    $tagArray[] = $oneTag;
                }
            }
        }
        return $tagArray;
    }


}