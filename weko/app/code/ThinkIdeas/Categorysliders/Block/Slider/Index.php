<?php

namespace ThinkIdeas\Categorysliders\Block\Slider;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\ProductFactory;

class Index extends \Magento\Catalog\Block\Product\AbstractProduct
{

    protected $_categoryHelper;
    protected $categoryFlatConfig;
    protected $delteorders;
    protected $_catalogConfig;
    protected $_storeManager;
    protected $categoryFactory;
    protected $productFactory;
    protected $_productsCollectionFactory;
    protected $_catalogProductVisibility;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Helper\Category $categoryHelper
     * @param array $data
     */
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, \Magento\Catalog\Block\Product\Context $procontext, \Magento\Catalog\Helper\Category $categoryHelper, \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState, CategoryRepositoryInterface $categoryRepository, ProductFactory $productFactory, \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility, \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productsCollectionFactory, \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $catecolls, array $data = [], $deleteorder = ['none']
    )
    {
        $this->productFactory = $productFactory;
        $this->_categoryHelper = $categoryHelper;
        $this->categoryFlatConfig = $categoryFlatState;
        $this->delteorders = $deleteorder;
        $this->_catalogConfig = $procontext->getCatalogConfig();
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->categoryRepository = $categoryRepository;
        $this->_productCollectionFactory = $productsCollectionFactory;
        $this->categoryFactory = $catecolls;
        parent::__construct($procontext, $data);
        
    }

    protected function _addProductAttributesAndPrices(
    \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
    )
    {
        return $collection
                        ->addMinimalPrice()
                        ->addFinalPrice()
                        ->addTaxPercents()
                        ->addAttributeToSelect($this->_catalogConfig->getProductAttributes())
                        ->addUrlRewrite();
    }

    public function productCollections($categoriesid)
    {

        if (!$categoriesid) {
            return;
        }
        $collection = $this->_productCollectionFactory->create();
        

        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $collection = $this->_addProductAttributesAndPrices($collection);        
        $collection->addCategoriesFilter(['in' => $categoriesid]);
        $collection->addStoreFilter($this->_storeManager->getStore()->getId());
        $collection->getSelect()->order('rand()');
        return $collection;
    }

    public function loadcate()
    {

        return $this->categoryFactory->create()
                        ->addIsActiveFilter()
                        ->addAttributeToSelect('*')
                        ->addAttributeToFilter('homepage_cate_slider', '1')
                        //->addFieldToFilter('noofproductsinslider', ['notnull' => 1])
                        ->setOrder('catesliderpos', 'ASC')
                        ->setStore($this->_storeManager->getStore());

    }

    /**
     * Return categories helper
     */
    public function getCategoryHelper()
    {
        return $this->_categoryHelper;
    }

    /**
     * Return categories helper
     * getHtml($outermostClass = '', $childrenWrapClass = '', $limit = 0)
     * example getHtml('level-top', 'submenu', 0)
     */
    public function getHtml()
    {
        return $this->topMenu->getHtml();
    }

    /**
     * Retrieve current store categories
     *
     * @param bool|string $sorted
     * @param bool $asCollection
     * @param bool $toLoad
     * @return \Magento\Framework\Data\Tree\Node\Collection|\Magento\Catalog\Model\Resource\Category\Collection|array
     */
    public function getStoreCategories($sorted = false, $asCollection = false, $toLoad = true)
    {
        return $this->_categoryHelper->getStoreCategories($sorted, $asCollection, $toLoad);
    }

    /**
     * Retrieve child store categories
     *
     */
    public function getChildCategories($category)
    {
        if ($this->categoryFlatConfig->isFlatEnabled() && $category->getUseFlatResource()) {
            $subcategories = (array) $category->getChildrenNodes();
        } else {
            $subcategories = $category->getChildren();
        }
        return $subcategories;
    }

}
