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
 * Class Category
 * @package ThinkIdeas\Faq\Model
 */
class Category extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var int|null
     */
    protected $_storeViewId = null;
    /**
     * @var CategoryFactory
     */
    protected $_categoryFactory;
    /**
     * @var CategoryvalueFactory
     */
    protected $_valueFactory;
    /**
     * @var ResourceModel\Categoryvalue\CollectionFactory
     */
    protected $_categoryValueCollectionFactory;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\Category $resource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param CategoryFactory $categoryFactory
     * @param CategoryvalueFactory $valueFactory
     * @param ResourceModel\Categoryvalue\CollectionFactory $categoryValueCollectionFactory
     * @param ResourceModel\Category\Collection $resourceCollection
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \ThinkIdeas\Faq\Model\ResourceModel\Category $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \ThinkIdeas\Faq\Model\CategoryFactory $categoryFactory,
        \ThinkIdeas\Faq\Model\CategoryvalueFactory $valueFactory,
        \ThinkIdeas\Faq\Model\ResourceModel\Categoryvalue\CollectionFactory $categoryValueCollectionFactory,
        \ThinkIdeas\Faq\Model\ResourceModel\Category\Collection $resourceCollection
    )
    {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
        $this->_storeManager = $storeManager;
        $this->_categoryFactory = $categoryFactory;
        $this->_valueFactory = $valueFactory;
        $this->_categoryValueCollectionFactory = $categoryValueCollectionFactory;
        if ($storeViewId = $this->_storeManager->getStore()->getId()) {
            $this->_storeViewId = $storeViewId;
        }
    }

    /**
     * @return array
     */
    public function getStoreAttributes() {
        return array(
            'name',
            'ordering',
            'status',
        );
    }

    /**
     * @return int|null
     */
    public function getStoreViewId() {
        return $this->_storeViewId;
    }

    /**
     * @param $storeViewId
     * @return $this
     */
    public function setStoreViewId($storeViewId) {
        $this->_storeViewId = $storeViewId;
        return $this;
    }

    /**
     * @return $this
     */
    public function beforeSave() {
        if ($this->getStoreViewId()) {
            $defaultStore = $this->_categoryFactory->create()->setStoreViewId(null)->load($this->getId());
            $storeAttributes = $this->getStoreAttributes();
            $data = $this->getData();
            foreach ($storeAttributes as $attribute) {
                if (isset($data['use_default']) && isset($data['use_default'][$attribute])) {
                    $this->setData($attribute . '_in_store', false);
                } else {
                    $this->setData($attribute . '_in_store', true);
                    $this->setData($attribute . '_value', $this->getData($attribute));
                }
                $this->setData($attribute, $defaultStore->getData($attribute));

            }
        }
        return parent::beforeSave();
    }

    /**
     * @return $this
     */
    public function afterSave() {
        $storeViewId = $this->getStoreViewId();
        if ($storeViewId) {
            $storeAttributes = $this->getStoreAttributes();
            foreach ($storeAttributes as $attribute) {
                $attributeValue = $this->_valueFactory->create()
                    ->loadAttributeValue($this->getId(), $storeViewId, $attribute);
                if ($this->getData($attribute . '_in_store')) {
                    try {

                        $attributeValue->setValue($this->getData($attribute . '_value'))->save();
                    } catch (\Exception $e) {

                    }
                } elseif ($attributeValue && $attributeValue->getId()) {
                    try {
                        $attributeValue->delete();
                    } catch (\Exception $e) {

                    }
                }
            }

        }
        return parent::afterSave();
    }

    /**
     * @param int $id
     * @param null $field
     * @return $this
     */
    public function load($id, $field = null) {
        parent::load($id, $field);
        if ($this->getStoreViewId()) {
            $this->getStoreViewValue();
        }
        return $this;
    }

    /**
     * @param null $storeViewId
     * @return $this
     */
    public function getStoreViewValue($storeViewId = null) {
        if (!$storeViewId) {
            $storeViewId = $this->getStoreViewId();
        }
        if (!$storeViewId) {
            return $this;
        }
        $storeValues = $this->_categoryValueCollectionFactory->create()
            ->addFieldToFilter('category_id', $this->getId())
            ->addFieldToFilter('store_id', $storeViewId);
        foreach ($storeValues as $value) {
            $this->setData($value->getAttributeCode() . '_in_store', true);
            $this->setData($value->getAttributeCode(), $value->getValue());
        }
        return $this;
    }

}
