<?php

namespace ThinkIdeas\Additional\Block\Product;

use \Amasty\Shopby\Helper\FilterSetting;
use \Magento\Store\Model\ScopeInterface;

class View extends \Magento\Catalog\Block\Product\View
{
        public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Block\Product\Context $productContext,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
        \Amasty\Shopby\Helper\OptionSetting $optionSetting,

        array $data = []
    ) {

        $this->_storeManager = $context->getStoreManager();
        $this->_optionSettingHelper = $optionSetting;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_blockFactory = $blockFactory;

        parent::__construct($productContext, $urlEncoder, $jsonEncoder, $string, $productHelper, $productTypeConfig, $localeFormat, $customerSession, $productRepository, $priceCurrency, $data);
    }

    public function getProductData()
    {
        $product = $this->getProduct();

        return $product;    
    }

    public function getBrandIcon()
    {
        $product = $this->getProductData();

        $html = "";

        if ($product instanceof \Magento\Catalog\Model\Product) {
            if ($product->getId()) {
                $brandCode = $this->_scopeConfig->getValue('amshopby_brand/general/attribute_code', ScopeInterface::SCOPE_STORE);
                $attribute = $product->getResource()->getAttribute($brandCode);
                $storeId = $this->_storeManager->getStore()->getId();
                $optionId = (int) $product->getResource()->getAttributeRawValue($product->getId(), $attribute, $storeId);
                $filterCode = FilterSetting::ATTR_PREFIX . $brandCode;
                $setting = $this->_optionSettingHelper->getSettingByValue($optionId, $filterCode, $storeId);
                if ($setting && $setting->getId() && $setting->getImageUrl()) {
                    return $setting;
                }
            }
        }

        return $html;
    }
}