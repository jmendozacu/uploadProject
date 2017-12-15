<?php

/**
 * Magedelight
 * Copyright (C) 2016 Magedelight <info@magedelight.com>.
 *
 * NOTICE OF LICENSE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see http://opensource.org/licenses/gpl-3.0.html.
 *
 * @category Magedelight
 *
 * @copyright Copyright (c) 2016 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\Looknbuy\Block;

use Magento\Framework\App\Filesystem\DirectoryList;

class Looks extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Default toolbar block name.
     *
     * @var string
     */
    protected $_defaultToolbarBlock = 'Magento\Catalog\Block\Product\ProductList\Toolbar';

    /**
     * @var array
     */
    protected $_priceBlock = [];

    /**
     * Product factory.
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlHelper;

    /**
     * @var \Magento\Checkout\Helper\Cart
     */
    protected $_cartHelper;

    /**
     * @var ReviewRendererInterface
     */
    protected $reviewRenderer;

    /**
     * @var \Magento\Wishlist\Helper\Data
     */
    protected $_wishlistHelper;

    /**
     * Review model factory.
     *
     * @var \Magento\Review\Model\ReviewFactory
     */
    protected $_reviewFactory;

    /**
     * @param \Magento\Catalog\Block\Product\Context             $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Customer\Model\Session                    $customerSession
     * @param \Magento\Catalog\Model\ProductFactory              $productFactory
     * @param \Magento\Framework\Url\Helper\Data                 $urlHelper
     * @param array                                              $data
     */
    public function __construct(
    \Magento\Catalog\Block\Product\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Catalog\Model\ProductFactory $productFactory, \Magento\Framework\Url\Helper\Data $urlHelper, \Magento\Review\Model\ReviewFactory $reviewFactory, \Magento\Framework\Image\AdapterFactory $imageFactory, array $data = []
    ) {
        $this->imageBuilder = $context->getImageBuilder();
        $this->scopeConfig = $context->getScopeConfig();
        $this->customerSession = $customerSession;
        $this->_productFactory = $productFactory;
        $this->urlHelper = $urlHelper;
        $this->_cartHelper = $context->getCartHelper();
        $this->reviewRenderer = $context->getReviewRenderer();
        $this->_wishlistHelper = $context->getWishlistHelper();
        $this->_reviewFactory = $reviewFactory;
        $this->_filesystem = $context->getFilesystem();

        $this->_storeManager = $context->getStoreManager();

        $this->_directory = $context->getFilesystem()->getDirectoryWrite(DirectoryList::MEDIA);

        $this->_imageFactory = $imageFactory;

        parent::__construct($context, $data);

        
    }

    public function getlooksCollection()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $collections = $objectManager->create('\Magedelight\Looknbuy\Model\Looknbuy')
                        ->getCollection()
                        ->addFieldToSelect('*')->addFieldToFilter('status', array('eq' => 1))
                        ->addFieldToFilter('is_homepage', array('eq' => 0));


                        // echo"<pre/>"; print_r($collections->getData());exit;
        return $collections;
    }
    public function _prepareLayout()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $title = $this->scopeConfig->getValue('looknbuy/general/heading_title', $storeScope);
        $this->pageConfig->getTitle()->set(__($title));

        parent::_prepareLayout();
        // if ($this->getCollection()) {
        //     echo"<pre/>"; print_r($this->getCollection()->getData());exit;


        //     // create pager block for collection 

        //     $toolbar = $this->getToolbarBlock();

        //     // $pager = $this->getLayout()->createBlock(
        //     //                 'Magento\Theme\Block\Html\Pager', 'list.pager'
        //     //         )->setCollection(
        //     //         $this->getCollection() // assign collection to pager
        //     // );
        //     // $toolbar->setChild('product_list_toolbar_pager', $pager); // set pager block in layout
        //     // called prepare sortable parameters
        //     $collection = $this->getCollection();

        //     // use sortable parameters
        //     $orders = $this->getAvailableOrders();

        //     // if ($orders) {
        //     //     $toolbar->setAvailableOrders($orders);
        //     // }
        //     $sort = $this->getSortBy();
        //     if ($sort) {
        //         $toolbar->setDefaultOrder($sort);
        //     }
        //     // $dir = $this->getDefaultDirection();
        //     // if ($dir) {
        //     //     $toolbar->setDefaultDirection($dir);
        //     // }
        //     $modes = $this->getModes();
        //     if ($modes) {
        //         $toolbar->setModes($modes);
        //     }
        //     $toolbar->setCollection($collection);

        //     $this->setChild('toolbar', $toolbar);
        //     $this->getCollection()->load();
        // }
        // 
        return $this;

        //return parent::_prepareLayout();
    }

    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    public function getToolbarBlock()
    {
        $blockName = $this->getToolbarBlockName();
        if ($blockName) {
            $block = $this->getLayout()->getBlock($blockName);
            if ($block) {
                return $block;
            }
        }
        $block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, uniqid(microtime()));

        return $block;
    }

    public function getAvailableOrders()
    {
        return array(
            'look_name' => __('Look Name'),
            'discount_price' => __('Discount'),
        );
    }

    public function imageResize($image)
    {
        // $absPath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath().$image;
        // $imageResized = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('resized/').$image;
        // $imageResize = $this->_imageFactory->create();
        // $imageResize->open($absPath);
        // $imageResize->constrainOnly(true);
        // $imageResize->keepTransparency(true);
        // $imageResize->keepFrame(false);
        // $imageResize->keepAspectRatio(true);
        // $imageResize->resize(240, 300);
        // $dest = $imageResized;
        // $imageResize->save($dest);
        $resizedURL = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).$image;

        return $resizedURL;
    }

    /**
     * Retrieve current view mode.
     *
     * @return string
     */
    public function getMode()
    {
        return $this->getChildBlock('toolbar')->getCurrentMode();
    }
}
