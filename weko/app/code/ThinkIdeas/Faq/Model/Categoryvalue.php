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
 * Class Categoryvalue
 * @package ThinkIdeas\Faq\Model
 */
class Categoryvalue extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var ResourceModel\Categoryvalue\CollectionFactory
     */
    protected $_categoryValueCollectionFactory;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\Categoryvalue $resource
     * @param ResourceModel\Categoryvalue\Collection $resourceCollection
     * @param ResourceModel\Categoryvalue\CollectionFactory $categoryValueCollectionFactory
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \ThinkIdeas\Faq\Model\ResourceModel\Categoryvalue $resource,
        \ThinkIdeas\Faq\Model\ResourceModel\Categoryvalue\Collection $resourceCollection,
        \ThinkIdeas\Faq\Model\ResourceModel\Categoryvalue\CollectionFactory $categoryValueCollectionFactory
    )
    {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
        $this->_categoryValueCollectionFactory = $categoryValueCollectionFactory;
    }

    /**
     * @param $categoryId
     * @param $storeViewId
     * @param $attributeCode
     * @return $this
     */
    public function loadAttributeValue($categoryId, $storeViewId, $attributeCode)
    {
        $attributeValue = $this->_categoryValueCollectionFactory->create()
            ->addFieldToFilter('category_id', $categoryId)
            ->addFieldToFilter('store_id', $storeViewId)
            ->addFieldToFilter('attribute_code', $attributeCode)
            ->getFirstItem();
        $this->setData('category_id', $categoryId)
            ->setData('store_id', $storeViewId)
            ->setData('attribute_code', $attributeCode);
        if ($attributeValue) {
            $this->addData($attributeValue->getData())
                ->setId($attributeValue->getId());
        }

        return $this;
    }
}
