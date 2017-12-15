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
 * Class Faq
 * @package ThinkIdeas\Faq\Model
 */
class Faq extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var FaqvalueFactory
     */
    protected $_valueFactory;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var FaqFactory
     */
    protected $_faqFactory;
    /**
     * @var ResourceModel\Faqvalue\CollectionFactory
     */
    protected $_faqValueCollectionFactory;
    /**
     * @var \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory
     */
    protected $_urlRewriteCollectionFactory;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\Faq $resource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param FaqvalueFactory $valueFactory
     * @param \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory $urlRewriteCollectionFactory
     * @param FaqFactory $faqFactory
     * @param ResourceModel\Faqvalue\CollectionFactory $faqValueCollectionFactory
     * @param ResourceModel\Faq\Collection $resourceCollection
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \ThinkIdeas\Faq\Model\ResourceModel\Faq $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \ThinkIdeas\Faq\Model\FaqvalueFactory $valueFactory,
        \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory $urlRewriteCollectionFactory,
        \ThinkIdeas\Faq\Model\FaqFactory $faqFactory,
        \ThinkIdeas\Faq\Model\ResourceModel\Faqvalue\CollectionFactory $faqValueCollectionFactory,
        \ThinkIdeas\Faq\Model\ResourceModel\Faq\Collection $resourceCollection
    )
    {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
        $this->_storeManager = $storeManager;
        $this->_faqFactory = $faqFactory;
        $this->_valueFactory = $valueFactory;
        $this->_faqValueCollectionFactory = $faqValueCollectionFactory;
        $this->_urlRewriteCollectionFactory = $urlRewriteCollectionFactory;
        if ($storeViewId = $this->_storeManager->getStore()->getId()) {
            $this->_storeViewId = $storeViewId;
        }
    }

    /**
     * @return array
     */
    public function getStoreAttributes()
    {
        return array(
            'title',
            'description',
            'status',
            'ordering',
            'tag',
            'metakeyword',
            'metadescription'
        );
    }

    /**
     * @return int
     */
    public function getStoreViewId()
    {
        return $this->_storeViewId;
    }

    /**
     * @param $storeViewId
     * @return $this
     */
    public function setStoreViewId($storeViewId)
    {
        $this->_storeViewId = $storeViewId;
        return $this;
    }

    /**
     * @return $this
     */
    public function beforeSave()
    {
        if ($this->getStoreViewId()) {
            $defaultStore = $this->_faqFactory->create()->setStoreViewId(null)->load($this->getId());
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
    public function afterSave()
    {
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
    public function load($id, $field = null)
    {
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
    public function getStoreViewValue($storeViewId = null)
    {
        if (!$storeViewId) {
            $storeViewId = $this->getStoreViewId();
        }
        if (!$storeViewId) {
            return $this;
        }
        $storeValues = $this->_faqValueCollectionFactory->create()
            ->addFieldToFilter('faq_id', $this->getId())
            ->addFieldToFilter('store_id', $storeViewId);
        foreach ($storeValues as $value) {
            $this->setData($value->getAttributeCode() . '_in_store', true);
            $this->setData($value->getAttributeCode(), $value->getValue());
        }
        return $this;
    }

    /**
     *
     */
    public function updateUrlKey()
    {
        $id = $this->getId();
        $allStore = $this->_storeManager->getStores();
        foreach ($allStore as $store) {
            $store_id = $store->getStoreId();
            if ($store_id) {
                $urlRewrite = $this->_urlRewriteCollectionFactory->create()
                    ->addFieldToFilter('entity_type', 'faq')
                    ->addFieldToFilter('entity_id', $id)
                    ->addFieldToFilter('store_id', $store_id)
                    ->getFirstItem();
                $urlRewrite->setData("entity_type", "faq");
                $urlRewrite->setData("entity_id", $id);
                $urlRewrite->setData("request_path", $this->getData('url_key'));
                $urlRewrite->setData("target_path", 'faq/index/index/id/' . $id);
                $urlRewrite->setData("store_id", $store_id);
                $urlRewrite->setData("is_autogenerated", 0);

                try {
                    $urlRewrite->save();
                } catch (\Exception $e) {
                    try {
                        $temp = $this->getData('url_key');
                        $explodeUrl = explode('.', $temp);
                        $before = '';
                        $after = '';
                        foreach ($explodeUrl as $key => $value) {
                            if ($key == (sizeof($explodeUrl) - 2)) {
                                $after = $explodeUrl[sizeof($explodeUrl) - 2];
                            }
                            if ($key < (sizeof($explodeUrl) - 3)) {
                                $before = $before . $value;
                            }
                        }
                        $temp = $before . $after . '-' . $id . '.html';
                        $urlRewrite->setData("request_path", $temp);
                        $urlRewrite->save();

                        $this->_faqFactory->create()->load($id)
                            ->setUrlKey($temp)
                            ->save();
                        $this->setUrlKey($temp)
                            ->save();
                    } catch (\Exception $e) {

                    }
                }
            }
        }
    }

    /**
     *
     */
    public function deleteAllUrlKey()
    {
        try {
            $id = $this->getId();
            $urlRewrite = $this->_urlRewriteCollectionFactory->create()
                ->addFieldToFilter('entity_type', 'faq')
                ->addFieldToFilter('entity_id', $id);
            foreach ($urlRewrite as $urlModel) {
                try {
                    $urlModel->delete();
                } catch (\Exception $e) {

                }
            }
        } catch (\Exception $e) {

        }
    }
}
