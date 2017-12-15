<?php
/**
 * ThinkIdeas
 *
 * @category    ThinkIdeas
 * @package     ThinkIdeas_Faq
 *
 */
namespace ThinkIdeas\Faq\Helper;
/**
 * Class Data
 * @package ThinkIdeas\Faq\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	/**
	 * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
	protected $_localeDate;
	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
     */
	protected $_storeManager;
	/**
	 * @var \Magento\Framework\Filter\Template
     */
	protected $_filterTemplate;
	/**
	 * @var \ThinkIdeas\Faq\Model\ResourceModel\Faq\CollectionFactory
     */
	protected $_faqCollectionFactory;
	/**
	 * @var \Magento\Cms\Model\Template\FilterProvider
     */
	protected $_filterProvider;
	/**
	 * @var \Magento\Framework\ObjectManagerInterface
     */
	protected $_objectManager;
	/**
	 * @var \ThinkIdeas\Faq\Model\ResourceModel\Category\CollectionFactory
     */
	protected $_categoryCollectionFactory;

	/**
	 * @param \Magento\Framework\App\Helper\Context $context
	 * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
	 * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
	 * @param \Magento\Framework\Filter\Template $filterTemplate
	 * @param \ThinkIdeas\Faq\Model\ResourceModel\Faq\CollectionFactory $faqCollectionFactory
	 * @param \Magento\Framework\ObjectManagerInterface $objectManager
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \ThinkIdeas\Faq\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     */
	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
		\Magento\Cms\Model\Template\FilterProvider $filterProvider,
		\Magento\Framework\Filter\Template $filterTemplate,
		\ThinkIdeas\Faq\Model\ResourceModel\Faq\CollectionFactory $faqCollectionFactory,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\ThinkIdeas\Faq\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
	)
	{
		$this->_storeManager = $storeManager;
		$this->_localeDate = $localeDate;
		$this->_filterTemplate = $filterTemplate;
		$this->_filterProvider = $filterProvider;
		$this->_faqCollectionFactory = $faqCollectionFactory;
		$this->_objectManager = $objectManager;
		$this->_categoryCollectionFactory = $categoryCollectionFactory;
		parent::__construct($context);
	}

	/**
	 * @return callable|null
     */
	public function getTemplateProcessor() {
		return $this->_filterTemplate->getTemplateProcessor();
	}

	/**
	 * @param $html
	 * @return string
	 * @throws \Exception
     */
	public function getFilter($html) {
		return $this->_filterProvider->getPageFilter()->filter($html);
	}

	/**
	 * @return mixed
     */
	public function isEnableConfig()
	{
		return $this->scopeConfig->getValue('faq/general/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * @return mixed
     */
	public function getPageSizeNumber()
	{
		return $this->scopeConfig->getValue('faq/general/page_size_number', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * @return mixed
     */
	public function getSidebarPosition()
	{
		return $this->scopeConfig->getValue('faq/general/sidebar_position', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * @return mixed
     */
	public function isShowSidebar()
	{
		return $this->scopeConfig->getValue('faq/general/show_sidebar', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * @return mixed
     */
	public function getSidebarQuestionNumber()
	{
		return $this->scopeConfig->getValue('faq/general/sidebar_question_number', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * @return mixed
     */
	public function getTagNumber()
	{
		return $this->scopeConfig->getValue('faq/general/tag_number', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}


	/**
	 * @return mixed
     */
	public function getMetaKeywords()
	{
		return $this->scopeConfig->getValue('faq/general/meta_keywords', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}


	/**
	 * @return mixed
     */
	public function getMetaDescription()
	{
		return $this->scopeConfig->getValue('faq/general/meta_description', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}


	/**
	 * @return mixed
     */
	public function getStoreId() {
		return $this->_storeManager->getStore(true)->getStoreId();
	}


	/**
	 * @param null $storeId
	 * @return null|string
     */
	public function getHtmlTags($storeId = null){
		$faqCollection= $this->_faqCollectionFactory->create()
			->setStoreViewId($this->_storeManager->getStore(true)->getStoreId())
			->addFieldToFilter('status',1);
		$tagArray = array();
		foreach ($faqCollection as $faq) {
			$tag = $faq->getTag();
			$tagArrayFaq = explode(',',$tag);
			foreach ($tagArrayFaq as $oneTag) {
				$oneTag = trim($oneTag);
				if ($oneTag && !in_array($oneTag,$tagArray)) {
					$tagArray[] = $oneTag;
				}
			}
		}
		if(!count($tagArray))  {
			return null;
		}
		$_html = '';
		$_html .= '<'.'div id="box-tags"
            style="display:none;float: left;
                    width: 100%;
                    margin-top: 11px;
                    border: 1px solid #c8c8c8;
                    padding: 15px;
                    box-sizing: border-box;
                    position: relative;">';
		foreach($tagArray as $key => $tag){
			$_html .= '<a style="
                        float: left;
                        color: #3e6d8e;
                        font-size: 12px;
                        white-space: nowrap;
                        background: #e4edf4;
                        border: 1px solid #e4edf4;
                        display: inline-block;
                        margin: 2px 2px 2px 0;
                        border-radius: 0;
                        -webkit-transition: color 0.15s ease, background 0.15s ease, border 0.15s ease;
                        -moz-transition: color 0.15s ease, background 0.15s ease, border 0.15s ease;
                        -ms-transition: color 0.15s ease, background 0.15s ease, border 0.15s ease;
                        -o-transition: color 0.15s ease, background 0.15s ease, border 0.15s ease;
                        line-height: 1;
                        text-decoration: none;
                        text-align: center;
                        padding: .4em .5em;
                        cursor: pointer;

                    "'.' id=sug-'.$key.' class="post-tag" key="'.$key.'" >'.$tag.'</a> ';
		}
		$_html .= '</br><'.'a style="  position: absolute;top: 5px;right: 5px;" id="close-box" href="javascript:void(0)" >[X]</a></div>' ;
		return $_html;
	}

	/**
	 * @param $urlKey
	 * @return string
     */
	public function normalizeUrlKey($urlKey)
	{
		$urlKeyEx = explode('.', $urlKey);
		$end = end($urlKeyEx);
		if($end =='html' || $end=='htm'){
			unset($urlKeyEx[count($urlKeyEx)-1]);
			$urlKey = $this->_objectManager->create('Magento\Catalog\Model\Product\Url')
							->formatUrlKey(implode('.', $urlKeyEx)).'.html';
		}else{
			$urlKey =  $this->_objectManager->create('Magento\Catalog\Model\Product\Url')
					->formatUrlKey(implode('.', $urlKeyEx));
		}

		$faq = $this->_faqCollectionFactory->create()->addFieldToFilter('url_key',$urlKey);

		if(count($faq) > 0)
		{
			//$faq = $faq->getFirstItem();
			// $url =  $faq->getUrlKey();
			$url =  $urlKey;
			$explodeUrl = explode('.',$url);
			$before = '';
			$after = '';
			foreach ($explodeUrl as $key => $value) {
				if(sizeof($explodeUrl) == 1)
				{
					$before = $value;
				}
				else
				{
					if($key == (sizeof($explodeUrl) -2)){
						$after = $explodeUrl[sizeof($explodeUrl) -2];
					}
					if($key < (sizeof($explodeUrl) -3)){
						$before = $before.$value;
					}
				}
			}
			$result = $before.$after.'.html';
			return $result;
		} else {
			return $urlKey;
		}
	}
}
