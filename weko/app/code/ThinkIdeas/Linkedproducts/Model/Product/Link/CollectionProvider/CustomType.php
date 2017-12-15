<?php

namespace ThinkIdeas\Linkedproducts\Model\Product\Link\CollectionProvider;

class CustomType implements \Magento\Catalog\Model\ProductLink\CollectionProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getLinkedProducts(\Magento\Catalog\Model\Product $product)
    {
        return $product->getCustomTypeProducts();
    }
}