<?php

namespace ThinkIdeas\Linkedproducts\Model\Catalog\Product\Link;

class Proxy extends \Magento\Catalog\Model\Product\Link\Proxy
{
    /**
     * {@inheritdoc}
     */
    public function useCustomtypeLinks()
    {
        return $this->_getSubject()->useCustomtypeLinks();
    }
}