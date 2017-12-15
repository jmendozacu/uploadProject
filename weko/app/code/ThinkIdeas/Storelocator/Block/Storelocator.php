<?php
declare(strict_types=1);
/**
 * ThinkIdeas_Storelocator extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  ThinkIdeas
 * @package   ThinkIdeas_Storelocator
 * @copyright 2016 Claudiu Creanga
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Claudiu Creanga
 */
 
namespace ThinkIdeas\Storelocator\Block;

use Magento\Backend\Block\Template\Context;
use ThinkIdeas\Storelocator\Model\Stores;
use ThinkIdeas\Storelocator\Model\ResourceModel\Stores\CollectionFactory as StorelocatorCollectionFactory;
use ThinkIdeas\Storelocator\Model\ResourceModel\Stores\Collection;
use Magento\Directory\Model\CountryFactory;
use Magento\Directory\Model\Config\Source\Country;
use Magento\Store\Model\ScopeInterface;
use Magento\Backend\App\Action;

class Storelocator extends \Magento\Directory\Block\Data
{
        
    /**
     * @var string
     */
    const MAP_STYLES_CONFIG_PATH = 'thinkideas_storelocator/stockist_map/map_style';
            
    /**
     * @var string
     */
    const MAP_PIN_CONFIG_PATH = 'thinkideas_storelocator/stockist_map/map_pin';
            
    /**
     * @var string
     */
    const ASK_LOCATION_CONFIG_PATH = 'thinkideas_storelocator/stockist_map/ask_location';
    
    /**
     * @var string
     */
    const TEMPLATE_CONFIG_PATH = 'thinkideas_storelocator/stockist_map/template';
    
    /**
     * @var string
     */
    const UNIT_LENGTH_CONFIG_PATH = 'thinkideas_storelocator/stockist_map/unit_length';
        
    /**
     * @var int
     */
    const LATITUDE_CONFIG_PATH = 'thinkideas_storelocator/stockist_map/latitude';
            
    /**
     * @var int
     */
    const LONGITUDE_CONFIG_PATH = 'thinkideas_storelocator/stockist_map/longitude';
            
    /**
     * @var int
     */
    const ZOOM_CONFIG_PATH = 'thinkideas_storelocator/stockist_map/zoom';
            
    /**
     * @var string
     */
    const RADIUS_CONFIG_PATH = 'thinkideas_storelocator/stockist_map/radius';
            
    /**
     * @var string
     */
    const STROKE_WEIGHT_CONFIG_PATH = 'thinkideas_storelocator/stockist_radius/circle_stroke_weight';
    
    /**
     * @var string
     */
    const STROKE_OPACITY_CONFIG_PATH = 'thinkideas_storelocator/stockist_radius/circle_stroke_opacity';
        
    /**
     * @var string
     */
    const STROKE_COLOR_CONFIG_PATH = 'thinkideas_storelocator/stockist_radius/circle_stroke_color';
            
    /**
     * @var string
     */
    const FILL_OPACITY_CONFIG_PATH = 'thinkideas_storelocator/stockist_radius/circle_fill_opacity';
            
    /**
     * @var string
     */
    const FILL_COLOR_CONFIG_PATH = 'thinkideas_storelocator/stockist_radius/circle_fill_color';
    
    /**
     * @var StorelocatorCollectionFactory
     */
    public $storelocatorCollectionFactory;
        
    /**
     * @var Country
     */
    public $countryHelper;

    public $_registry;
    protected $_objectManager;

    protected $catalogSession;

    /**
     * Store factory
     *
     * @var StoreFactory
     */
    protected $_storeFactory;

    /**
     * @var \Magento\CheckoutAgreements\Model\ResourceModel\Agreement\CollectionFactory
     */
    protected $_agreementCollectionFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    protected $_checkoutSession;
    
    public function __construct(
        StorelocatorCollectionFactory $storelocatorCollectionFactory,
        Country $countryHelper,
        Context $context,
        \Magento\Framework\Registry $registry,
        \ThinkIdeas\Storelocator\Model\StoresFactory $storeFactory,
        \Magento\CheckoutAgreements\Model\ResourceModel\Agreement\CollectionFactory $agreementCollectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        array $data = []
    ) {
        $this->storelocatorCollectionFactory = $storelocatorCollectionFactory;
        $this->_agreementCollectionFactory   = $agreementCollectionFactory;
        $this->countryHelper                 = $countryHelper;
        $this->_registry                     = $registry;
        $this->_storeFactory                 = $storeFactory;
        $this->catalogSession                = $catalogSession;
        $this->_moduleManager                = $moduleManager;
        $this->_customerSession              = $customerSession;
        $this->_checkoutSession              = $checkoutSession;

        $this->_objectManager  = $objectmanager;

        parent::__construct(
            $context,
            $directoryHelper,
            $jsonEncoder,
            $configCacheType,
            $regionCollectionFactory,
            $countryCollectionFactory,
            $data
        );
    }
    
    public function getReservedProduct()
    {
        $product = $this->getSessionData("reserved");
        
        return $product;
    }

    public function getSessionData($key, $remove = false)
    {
        return $_SESSION['checkout'][$key];
    }
    public function isUserLoggedIn()
    {
        $customerSession = $this->_objectManager->get('Magento\Customer\Model\Session');
        
        if($customerSession->isLoggedIn()) {
           // echo"<pre/>"; print_r("test");exit;
           return true;
        }
        
        return false;
    }

    /**
     * Retrieve form data
     *
     * @return mixed
     */
    public function getFormData()
    {
        $data = $this->getData('form_data');
        if ($data === null) {
            $formData = $this->_customerSession->getCustomerFormData(true);
            $data = new \Magento\Framework\DataObject();
            if ($formData) {
                $data->addData($formData);
                $data->setCustomerData(1);
            }
            if (isset($data['region_id'])) {
                $data['region_id'] = (int)$data['region_id'];
            }
            $this->setData('form_data', $data);
        }
        return $data;
    }

    /**
     * Retrieve customer region identifier
     *
     * @return mixed
     */
    public function getRegion()
    {
        if (null !== ($region = $this->getFormData()->getRegion())) {
            return $region;
        } elseif (null !== ($region = $this->getFormData()->getRegionId())) {
            return $region;
        }
        return null;
    }
    
    /**
     * Retrieve customer country identifier
     *
     * @return int
     */
    public function getCountryId()
    {
        $countryId = $this->getFormData()->getCountryId();
        if ($countryId) {
            return $countryId;
        }
        return parent::getCountryId();
    }

    /**
     * Get config
     *
     * @param string $path
     * @return string|null
     */
    public function getConfig($path)
    {
        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

     /**
     * @return mixed
     */
    public function getAgreements()
    {
        
        if (!$this->hasAgreements()) {
            $agreements = [];

            if ($this->_scopeConfig->isSetFlag('checkout/options/enable_agreements', ScopeInterface::SCOPE_STORE)) {
                /** @var \Magento\CheckoutAgreements\Model\ResourceModel\Agreement\Collection $agreements */
                $agreements = $this->_agreementCollectionFactory->create();
                $agreements->addStoreFilter($this->_storeManager->getStore()->getId());
                $agreements->addFieldToFilter('is_active', 1);
                
            }
            $this->setAgreements($agreements);
        }
        return $this->getData('agreements');
    }

    /**
     * Newsletter module availability
     *
     * @return bool
     */
    public function isNewsletterEnabled()
    {
        return $this->_moduleManager->isOutputEnabled('Magento_Newsletter');
    }
     /**
      * return storelocator collection
      *
      * @return CollectionFactory
      */
    public function getStoresForFrontend(): Collection
    {
        $collection = $this->storelocatorCollectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('status', Stores::STATUS_ENABLED)
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->setOrder('stockist_id', 'ASC');

        $collection->getSelect()->limit(6);
        return $collection;
    }

    public function getLoadProduct($id)
    {
        $product = $this->_objectManager->get('Magento\Catalog\Model\Product')->load($id);


        return $product;
    }

    public function getActionUrl()
    {
        return $this->getUrl(
            'storelocator/index/create',
            [
                '_secure' => $this->getRequest()->isSecure()
            ]
        );
    }

    public function getLoginActionUrl()
    {
        return $this->getUrl(
            'customer/account/loginPost/',
            [
                '_secure' => $this->getRequest()->isSecure()
            ]
        );
    }
    public function getLoggedinActionUrl()
    {
        return $this->getUrl(
            'storelocator/account/loggedInPost/',
            [
                '_secure' => $this->getRequest()->isSecure()
            ]
        );
    }
    public function getPriceFormate($value='')
    {
            return $this->_objectManager->create('Magento\Framework\Pricing\Helper\Data'); // Instance of Pricing Helper

            
        }
    
    /**
     * @return array
     */
    public function _getSelectedProducts($id)
    {
        
        $store    = $this->_getStorelocator($id);
    
        $products =  $store->getProducts($store);

        return $products;
    }

    public function _getStorelocator($id)
    {
        $store   = $this->_storeFactory->create();
        if ($id) {
            $store->load($id);
        }
        return $store;
    } 

    /**
     * get an array of country codes and country names: AF => Afganisthan
     *
     * @return array
     */
    public function getCountries(): array
    {

        $loadCountries = $this->countryHelper->toOptionArray();
        $countries = [];
        $i = 0;
        foreach ($loadCountries as $country ) {
            $i++;
            if ($i == 1) { //remove first element that is a select
                continue;
            }
            $countries[$country["value"]] = $country["label"];
        }
        return $countries;
    }
    
    /**
     * get media url
     *
     * @return string
     */  
    public function getMediaUrl(): string
    {
	    return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
    }
    
    /**
     * get map style from configuration
     *
     * @return string
     */   
    public function getMapStyles(): string
    {
	    return $this->_scopeConfig->getValue(self::MAP_STYLES_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }
        
    /**
     * get map pin from configuration
     *
     * @return string or null
     */   
    public function getMapPin()
    {
	    return $this->_scopeConfig->getValue(self::MAP_PIN_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }
    
    /**
     * get location settings from configuration
     *
     * @return int
     */   
    public function getLocationSettings(): int
    {
	    return (int)$this->_scopeConfig->getValue(self::ASK_LOCATION_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }
        
    /**
     * get template settings from configuration, i.e full width or page width
     *
     * @return string
     */   
    public function getTemplateSettings(): string
    {
	    return $this->_scopeConfig->getValue(self::TEMPLATE_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }
        
    /**
     * get unit of length settings from configuration
     *
     * @return string
     */   
    public function getUnitOfLengthSettings(): string
    {
	    return $this->_scopeConfig->getValue(self::UNIT_LENGTH_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }
            
    /**
     * get zoom settings from configuration
     *
     * @return int
     */   
    public function getZoomSettings(): int
    {
	    return (int)$this->_scopeConfig->getValue(self::ZOOM_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }
            
    /**
     * get latitude settings from configuration
     *
     * @return float
     */   
    public function getLatitudeSettings(): float
    {
	    return (float)$this->_scopeConfig->getValue(self::LATITUDE_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }
            
    /**
     * get longitude settings from configuration
     *
     * @return float
     */   
    public function getLongitudeSettings(): float
    {
	    return (float)$this->_scopeConfig->getValue(self::LONGITUDE_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }
                
    /**
     * get radius settings from configuration
     *
     * @return float
     */   
    public function getRadiusSettings(): float
    {
	    return (float)$this->_scopeConfig->getValue(self::RADIUS_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }
                
    /**
     * get stroke weight settings from configuration
     *
     * @return float
     */   
    public function getStrokeWeightSettings(): float
    {
	    return (float)$this->_scopeConfig->getValue(self::STROKE_WEIGHT_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }
                
    /**
     * get stroke opacity settings from configuration
     *
     * @return float
     */   
    public function getStrokeOpacitySettings(): float
    {
	    return (float)$this->_scopeConfig->getValue(self::STROKE_OPACITY_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }
                
    /**
     * get stroke color settings from configuration
     *
     * @return string
     */   
    public function getStrokeColorSettings(): string
    {
	    return $this->_scopeConfig->getValue(self::STROKE_COLOR_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }
                
    /**
     * get fill opacity settings from configuration
     *
     * @return string
     */   
    public function getFillOpacitySettings(): float
    {
	    return (float)$this->_scopeConfig->getValue(self::FILL_OPACITY_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }
                
    /**
     * get fill color settings from configuration
     *
     * @return string
     */   
    public function getFillColorSettings(): string
    {
	    return $this->_scopeConfig->getValue(self::FILL_COLOR_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }
    
     /**
     * get base image url
     *
     * @return string
     */ 
    public function getBaseImageUrl(): string
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
    
    /**
     * get base image url
     *
     * @return string
     */ 
    public function getAjaxUrl(){
        return $this->getUrl("storelocator/index/index"); // Controller Url
    }
}