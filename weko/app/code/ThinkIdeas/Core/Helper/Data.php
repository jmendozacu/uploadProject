<?php
/**
 * @author ThinkIdeas Team
 * @copyright Copyright (c) 2016 Ktpl (https://www.krishtechnolabs.com)
 * @package ThinkIdeas_Core
 */

namespace ThinkIdeas\Core\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Store manager.
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     *
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $_productRepository;

    /**
     *
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;

    /**
     *
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $_cookieManagerInterface;

    /**
     *
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_datetime;

    /**
     * 
     * @var \Magento\Catalog\Model\Product\Attribute\Repository
     */
    protected $_productAttributeRepository;

    /**
     * 
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceCurrency; 
    
    /**
     * 
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * 
     * @var \Magento\Framework\Image\AdapterFactory
     */
    protected $_imageFactory;
    protected $_swatchHelper;
    protected $_swatchModel;
    protected $_cart;

    /**
     * [__construct description].
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param \Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManagerInterface
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $datetime
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Image\AdapterFactory $imageFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context, 
        \Magento\Catalog\Model\ProductRepository $productRepository, 
        \Magento\Backend\Model\UrlInterface $backendUrl, 
        \Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository, 
        \Magento\Store\Model\StoreManagerInterface $storeManager, 
        \Magento\Framework\Registry $registry, 
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManagerInterface, 
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime, 
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency, 
        \Magento\Framework\Filesystem $filesystem, 
        \Magento\Swatches\Helper\Media $swatchHelper,
        \Magento\Swatches\Model\ResourceModel\Swatch\Collection $swatchModel,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\Image\AdapterFactory $imageFactory
    ) {
        parent::__construct($context);
        $this->_productRepository           = $productRepository;
        $this->_backendUrl                  = $backendUrl;
        $this->_productAttributeRepository  = $productAttributeRepository;
        $this->_storeManager                = $storeManager;
        $this->_registry                    = $registry;
        $this->_cookieManagerInterface      = $cookieManagerInterface;
        $this->_datetime                    = $datetime;
        $this->_priceCurrency               = $priceCurrency;
        $this->_moduleManager               = $context->getModuleManager();
        $this->_filesystem                  = $filesystem;
        $this->_imageFactory                = $imageFactory;
        $this->_swatchHelper                = $swatchHelper;
        $this->_swatchModel                 = $swatchModel;
        $this->_cart                        = $cart;
        $this->_request                     = $context->getRequest();
        $this->scopeConfig                  = $context->getScopeConfig();
    }
    
    /**
     * Whether a module is enabled in the configuration or not
     *
     * @param string $moduleName Fully-qualified module name
     * @return boolean
     */
    public function isModuleEnabled($moduleName)
    {
        return $this->_moduleManager->isEnabled($moduleName);
    }
    
    /**
     * Whether a module output is permitted by the configuration or not
     *
     * @param string $moduleName Fully-qualified module name
     * @return boolean
     */
    public function isOutputEnabled($moduleName)
    {
        return $this->_moduleManager->isOutputEnabled($moduleName);
    }
    
    /**
     * get Base Url Media.
     *
     * @param string $path   [description]
     * @param bool   $secure [description]
     * @return string [description]
     */
    public function getBaseMediaUrl($path = '', $secure = false)
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA, $secure) . $path;
    }
    
    /**
     * get Backend Url
     * @param  string $route
     * @param  array  $params
     * @return string
     */
    public function getBackendUrl($route = '', $params = ['_current' => true])
    {
        return $this->_backendUrl->getUrl($route, $params);
    }
    
    /**
     * get New product label
     * @param date $fromDate [date from product is new]
     * @param date $toDate [date till product is new]
     * @return html [html for label]
     */
    public function getNewLabel($fromDate, $toDate)
    {
        $_newLable    = __('New');
        $_currentTime = strtotime($this->_datetime->date());
        if (isset($fromDate) && $_currentTime > strtotime($fromDate)) {
            if (isset($toDate)) {
                if (strtotime($toDate) > $_currentTime) {
                    $html = '<span class="new-label"><span>' . $_newLable . '</span></span>';
                } else {
                    $html = '';
                }
            } else {
                $html = '<span class="new-label"><span>' . $_newLable . '</span></span>';
            }
        } else {
            $html = '';
        }
        return $html;
    }
    
    /**
     * get Sale product label with discount amount in percentage
     * @param price [product final price]
     * @param price [product base price] 
     * @return html [html for label]
     */
    public function getSaleLabel($fianlPrice, $price)
    {
        $discountAmount     = $price - $fianlPrice;
        $discountPercentage = ($discountAmount * 100) / $price;
        $discountPercentage = round($discountPercentage);
        if ($discountPercentage) {
            $html = '<span class="sale-label"><span>' . $discountPercentage . '%' . '</span></span>';
        } else {
            $html = '';
        }
        return $html;
    }
    
    /**
     * Get store base currency code
     *
     * @return string
     */
    public function getBaseCurrencyCode()
    {
        return $this->_storeManager->getStore()->getBaseCurrencyCode();
    }
    
    /**
     * Get current store currency code
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        return $this->_storeManager->getStore()->getCurrentCurrencyCode();
    }
    
    /**
     * Get default store currency code
     *
     * @return string
     */
    public function getDefaultCurrencyCode()
    {
        return $this->_storeManager->getStore()->getDefaultCurrencyCode();
    }
    
    /**
     * Get allowed store currency codes
     *
     * If base currency is not allowed in current website config scope,
     * then it can be disabled with $skipBaseNotAllowed
     *
     * @param bool $skipBaseNotAllowed
     * @return array
     */
    public function getAvailableCurrencyCodes($skipBaseNotAllowed = false)
    {
        return $this->_storeManager->getStore()->getAvailableCurrencyCodes($skipBaseNotAllowed);
    }
    
    /**
     * Get array of installed currencies for the scope
     *
     * @return array
     */
    public function getAllowedCurrencies()
    {
        return $this->_storeManager->getStore()->getAllowedCurrencies();
    }
    
    /**
     * Get current currency rate
     *
     * @return float
     */
    public function getCurrentCurrencyRate()
    {
        return $this->_storeManager->getStore()->getCurrentCurrencyRate();
    }
    
    /**
     * Get current currency symbol
     *
     * @return string
     */
    public function getCurrentCurrencySymbol()
    {
        return $this->_priceCurrency->getCurrency()->getCurrencySymbol();
    }
    
    /**
     * get trimmed string
     * @param string [string to trim]
     * @param int [character count]
     * @return string [trimmed string]
     */
    public function getTrimmedString($string, $count)
    {
        return (strlen($string) > $count) ? substr($string, 0, ($count - 3)) . '...' : $string;
    }
    
    /**
     * Set redirect cookie 
     * @param string [key of cookie]
     * @param string [value of cookie]
     * @return set public cookie
     */
    public function setRedirectCookie($key, $value)
    {
        $this->_cookieManagerInterface->setPublicCookie($key, $value);
    }
    
    /**
     * Get current product from registry
     * @return current product object
     */
    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }

    /**
     * Get current category from registry
     * @return current category object
     */
    public function getCurrentCategory()
    {
        return $this->_registry->registry('current_category');
    }
    
    /**
     * to get attribute text value
     * @param object [product]
     * @param string [attribute code]
     * @param int [attribute option id]
     * @return string [attribute text value]
     */
    public function getAttributeValue($_product, $_attribute, $_optionId)
    {
        $attr = $_product->getResource()->getAttribute($_attribute);
        if ($attr->usesSource()) {
            return $optionText = $attr->getSource()->getOptionText($_optionId);
        }
    }
    
    /**
     * to get attribute name which assigned to product
     */
    public function getAttributeOptionTitle($attrbName, $attribValue)
    {
        $manufacturerOptions = $this->_productAttributeRepository->get($attrbName)->getOptions();
        foreach ($manufacturerOptions as $manufacturerOption) {
            if ($manufacturerOption->getValue() === $attribValue) {
                return $manufacturerOption->getLabel();
            }
        }
    }
    
    /**
     * check current store language and allow for rtl
     * 
     * @return string
     */
    public function isRtlSlider()
    {
        $isrtl = 'false';
        if (strpos($this->getStoreCode(), 'ar') !== false) {
            $isrtl = 'true';
        }
        return $isrtl;
    }
    
    /**
     * Get store identifier
     *
     * @return  int
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }
    
    /**
     * Get Store code
     *
     * @return string
     */
    public function getStoreCode()
    {
        return $this->_storeManager->getStore()->getCode();
    }
    
    /**
     * Get Store name
     *
     * @return string
     */
    public function getStoreName()
    {
        return $this->_storeManager->getStore()->getName();
    }
    
    /**
     * Get current url for store
     *
     * @param bool|string $fromStore Include/Exclude from_store parameter from URL
     * @return string     
     */
    public function getStoreUrl($fromStore = true)
    {
        return $this->_storeManager->getStore()->getUrl($fromStore);
    }
    
    /**
     * Check if store is active
     *
     * @return boolean
     */
    public function isStoreActive()
    {
        return $this->_storeManager->getStore()->isActive();
    }
    
    /**
     * Get website identifier
     *
     * @return string|int|null
     */
    public function getWebsiteId()
    {
        return $this->_storeManager->getStore()->getWebsiteId();
    }
    
    /**
     * Get list of all websites
     * 
     * return array [website codes]
     */
    public function getWebsites()
    {
        $_websites = $this->_storeManager->getWebsites();
        $websites  = array();
        foreach ($_websites as $_website) {
            array_push($websites, $_website->getCode());
        }
        return $websites;
    }
    
    /**
     * Get website name and url
     * @return array [website name, url]
     */
    public function getWebsitesNameAndUrl()
    {
        $_websites    = $this->_storeManager->getWebsites();
        $_websiteData = array();
        foreach ($_websites as $website) {
            foreach ($website->getStores() as $store) {
                $wedsiteId = $website->getId();
                $storeObj  = $this->_storeManager->getStore($store);
                $name      = $website->getName();
                $url       = $storeObj->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
                array_push($_websiteData, array(
                    'name' => $name,
                    'url' => $url
                ));
            }
        }
        return $_websiteData;
    }
    
    /**
     * get base url 
     * can be used to create url anywhere
     * no need to call objectmanager
     */
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK);
    }
    
    /**
     * Load product by id
     *
     * @param int [product id]
     * @return object [product object]
     */
    public function loadProductById($id)
    {
        return $this->_productRepository->getById($id);
    }
    
    /**
     * Load product by sku
     *
     * @param string [product sku]
     * @return object [product object]
     */
    public function loadProductBySku($sku)
    {
        return $this->_productRepository->get($sku);
    }
    
    /**
     * @param string [name]
     * @return string [name in lower case and replace space by underscore]
     */
    public function getStringSmallWithoutSpace($name)
    {
        return str_replace(array(' ','/'), array('_','_'), strtolower($name));
    }
    
    /**
     * replace only first occurance from string
     * @param string $find [find to replace]
     * @param string $replaceThis [with what you want to replace]
     * @param string $subject [string you want to change]
     * @return string
     */
    public function str_replace_first($find, $replaceThis, $subject)
    {
        $find = '/' . preg_quote($find, '/') . '/';
        return preg_replace($find, $replaceThis, $subject, 1);
    }
    
    /**
     * Get store configurations
     * @param string [config path]
     * @return configurations
     */
    public function getConfigData($config_path)
    {
        return $this->scopeConfig->getValue($config_path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    public function allowRendoring()
    {
        if ($this->_request->getFullActionName() == 'catalog_category_view') {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * resize image
     */
    public function resize($_sourcePath, $_destinationPath, $image, $width, $height)
    {
        if (!file_exists($this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath($_destinationPath) . str_replace('/', '_', $image))) {
            $absolutePath = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath($_sourcePath) . $image;
            $imageResized = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath($_destinationPath) . str_replace('/', '_', $image);
            $imageResize  = $this->_imageFactory->create();
            $imageResize->open($absolutePath);
            $imageResize->constrainOnly(TRUE);
            $imageResize->keepTransparency(TRUE);
            $imageResize->keepFrame(FALSE);
            $imageResize->keepAspectRatio(TRUE);
            $imageResize->resize($width, $height);
            $destination = $imageResized;
            $imageResize->save($destination);
        }
        $resizedURL = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $_destinationPath . str_replace('/', '_', $image);
        return $resizedURL;
    }
    
    /**
     * Filter one array with referance to another 
     */
    public function filterTopmenuattributes($_selectedOptions, $_allOptions)
    {
        $_result = array();
        foreach ($_selectedOptions as $key => $value) {
            if (array_key_exists($value, $_allOptions)) {
                $_result[$value] = $_allOptions[$value];
                unset($_allOptions[$value]);
            }
        }
        return $_result;
    }


    /**
     * Get attribute swatch image
     * @var $_product [product object]
     * @var $_optionId [attribute option id]
     * @return string [full path of image]
     */
    public function getSwatchImage($_product,$_optionId)
    {
        
        $this->_swatchModel->addFieldtoFilter('option_id',$_optionId);
        $item = $this->_swatchModel->getFirstItem();
        //return $this->_swatchHelper->getSwatchAttributeImage('swatch_thumb', $item->getValue());
        //return $this->_swatchHelper->getSwatchAttributeImage('swatch_image', $item->getValue());
        if(strlen($item->getValue()) > 0)
        {
            $imageUrl = $this->getBaseMediaUrl('attribute/swatch').$item->getValue();
            return $imageUrl;
        }
        
    }

    public function getCurrentCartItems()
    {
        $_itemsCount = $this->_cart->getQuote()->getItemsCount();
        return $_itemsCount;
    }

    public function getOrderTotal()
    {
        $_grandTotal = $this->_cart->getQuote()->getGrandTotal();
        //$_grandTotal = $this->_cart->getQuote()->getBaseSubtotal();
        //echo "<pre/>"; print_r($this->_cart->getQuote()->getBaseSubtotal());exit;
        return $_grandTotal;
    }
}