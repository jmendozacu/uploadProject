<?php

namespace ThinkIdeas\TipsOfExperts\Helper;

class Layout extends \Magento\Framework\App\Helper\AbstractHelper
{
	/**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected $_blockFactory;

    /**
     * @var \Zend_Filter_Interface
     */
    protected $templateProcessor;

    /**
     * @var \Amasty\Shopby\Helper\OptionSetting $optionSetting
     */
    protected $optionSetting;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Cms\Model\BlockFactory $blockFactory
     * @param \Amasty\Shopby\Helper\OptionSetting $optionSetting
     * @param \Zend_Filter_Interface $templateProcessor
     */
	public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\BlockFactory $blockFactory,
        \Amasty\Shopby\Helper\OptionSetting $optionSetting,
        \Zend_Filter_Interface $templateProcessor
    ) {
        $this->_storeManager = $storeManager;
        $this->_coreRegistry = $coreRegistry;
        $this->_blockFactory = $blockFactory;
        $this->templateProcessor = $templateProcessor;
        $this->optionSetting = $optionSetting;
        parent::__construct($context);
    }

    /**
     * get listing block based on layout
     *
     * @return array
     */
    public function getListingBlockItems()
    {
    	$loadedData = [];

        $blockLayout = ''; $currentCategory = $this->_getCurrentCategory();

        if(isset($currentCategory))
        {
            if($currentCategory->getId() == $this->_storeManager->getStore()->getRootCategoryId())
            {
                $attribute_code = 'manufacturer';
                $manufacturer = $this->_getRequest()->getParam($attribute_code);
                $filter_code = \Amasty\Shopby\Helper\FilterSetting::ATTR_PREFIX . $attribute_code;
                $setting = $this->optionSetting->getSettingByValue($manufacturer, $filter_code, $this->_storeManager->getStore()->getId());
                if($setting->getId())
                    $blockLayout = $setting->getBlockLayout();
            }
            else
                $blockLayout = $currentCategory->getBlockLayout();
        }
        
        if(isset($blockLayout) && $blockLayout != "")
        {
            $blockLayout = json_decode($blockLayout, true);

            $sortOrders = array_map(function($element) { return $element['position']; }, $blockLayout);

            foreach($blockLayout as $block)
            {
                $sortOrder = 0;
                if(isset($block['position']) && $block['position'] != "" && $block['position'] != "0")
                    $sortOrder = intval($block['position']);

                if($sortOrder == 0 || array_key_exists($sortOrder, $loadedData))
                    $sortOrder = $this->getSortOrder($loadedData, min($sortOrders));

                $loadedData[$sortOrder] = [
                    'list_block' => $block['list_block'],
                    'expert_block' => $block['expert_block']
                ];
            }
        }

    	return $loadedData;
    }

    /**
     * get unique key
     * 
     * @param array $loadedData
     * @param int $i
     * @return int
     */
    public function getSortOrder($loadedData, $i)
    {
        if($i == 0) $i = 1;

        while(array_key_exists($i, $loadedData))
            $i++;
        return $i;
    }

    /**
     * get current category object
     *
     * @return \Mage\Catalog\Model\Category
     */
    protected function _getCurrentCategory()
    {
    	return $this->_coreRegistry->registry('current_category');
    }

    /**
     * get the actual filter data
     *
     * @param HTML string
     * @return HTML output
     */
    public function filterOutputHtml($blockArray, $layout)
    {
        return $this->_getBlockHtml($blockArray['list_block'], $layout);
    }
    /**
     * get the actual filter data
     *
     * @param HTML string
     * @return HTML output
     */
    public function filterSubOutputHtml($blockArray, $layout)
    {
        return $this->_getBlockHtml($blockArray['expert_block'], $layout);
    }

    /**
     * Retrieve block content
     *
     * @param string $identifier
     * @return string
     */
    protected function _getBlockHtml($identifier, $layout)
    {
        $blockObject = $this->_blockFactory->create();
        $blockObject->load($identifier);
        if($blockObject->getId())
            return $this->templateProcessor->filter($blockObject->getContent());

        return '';
    }
}
