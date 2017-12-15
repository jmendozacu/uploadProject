<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ThinkIdeas\Linkedproducts\Model\Product\Type;

use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Simple product type implementation
 */
class Simple extends \Magento\Catalog\Model\Product\Type\Simple
{    

    /**
     * Attribute collection factory
     *
     * @var
     * \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Attribute\CollectionFactory
     */
     protected $_attributeCollectionFactory;

    /**
     * @codingStandardsIgnoreStart/End
     *
     * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Attribute\CollectionFactory $attributeCollectionFactory
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Catalog\Model\Product\Option $catalogProductOption,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\Product\Type $catalogProductType,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Registry $coreRegistry,
        \Psr\Log\LoggerInterface $logger,
        ProductRepositoryInterface $productRepository,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Attribute\CollectionFactory $attributeCollectionFactory
    ) {
        $this->_attributeCollectionFactory = $attributeCollectionFactory;

        parent::__construct(
            $catalogProductOption,
            $eavConfig,
            $catalogProductType,
            $eventManager,
            $fileStorageDb,
            $filesystem,
            $coreRegistry,
            $logger,
            $productRepository
        );        
    }

    /**
     * Retrieve configurable attribute collection
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Attribute\Collection
     */
    public function getConfigurableAttributeCollection(\Magento\Catalog\Model\Product $product)
    {
        return $this->_attributeCollectionFactory->create()->setProductFilter($product);
    }
}
