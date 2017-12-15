<?php
/**
 * ThinkIdeas
 *
 * @category    ThinkIdeas
 * @package     ThinkIdeas_Faq
 *
 */
namespace ThinkIdeas\Faq\Model;
/**
 * Class Faqvalue
 * @package ThinkIdeas\Faq\Model
 */
class Faqvalue extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var ResourceModel\Faqvalue\CollectionFactory
     */
    protected $_faqValueCollectionFactory;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\Faqvalue $resource
     * @param ResourceModel\Faqvalue\Collection $resourceCollection
     * @param ResourceModel\Faqvalue\CollectionFactory $faqValueCollectionFactory
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \ThinkIdeas\Faq\Model\ResourceModel\Faqvalue $resource,
        \ThinkIdeas\Faq\Model\ResourceModel\Faqvalue\Collection $resourceCollection,
        \ThinkIdeas\Faq\Model\ResourceModel\Faqvalue\CollectionFactory $faqValueCollectionFactory
    )
    {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
        $this->_faqValueCollectionFactory = $faqValueCollectionFactory;
    }

    /**
     * @param $categoryId
     * @param $storeViewId
     * @param $attributeCode
     * @return $this
     */
    public function loadAttributeValue($categoryId, $storeViewId, $attributeCode)
    {
        $attributeValue = $this->_faqValueCollectionFactory->create()
            ->addFieldToFilter('faq_id', $categoryId)
            ->addFieldToFilter('store_id', $storeViewId)
            ->addFieldToFilter('attribute_code', $attributeCode)
            ->getFirstItem();
        $this->setData('faq_id', $categoryId)
            ->setData('store_id', $storeViewId)
            ->setData('attribute_code', $attributeCode);
        if ($attributeValue) {
            $this->addData($attributeValue->getData())
                ->setId($attributeValue->getId());
        }

        return $this;
    }
}
