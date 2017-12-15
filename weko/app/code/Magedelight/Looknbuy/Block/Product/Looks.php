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

namespace Magedelight\Looknbuy\Block\Product;

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
     * @param \Magento\Catalog\Block\Product\Context  $context
     * @param \Magento\Framework\Url\Helper\Data      $urlHelper
     * @param \Magento\Framework\Image\AdapterFactory $imageFactory
     * @param array                                   $data
     */
    public function __construct(
    \Magento\Catalog\Block\Product\Context $context, \Magento\Framework\Url\Helper\Data $urlHelper, \Magento\Framework\Image\AdapterFactory $imageFactory, array $data = []
    ) {
        $this->imageBuilder = $context->getImageBuilder();

        $this->urlHelper = $urlHelper;
        $this->_filesystem = $context->getFilesystem();

        $this->_storeManager = $context->getStoreManager();

        $this->_directory = $context->getFilesystem()->getDirectoryWrite(DirectoryList::MEDIA);

        $this->_imageFactory = $imageFactory;

        parent::__construct($context, $data);

        $product_id = $this->getRequest()->getParam('id');

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $collections = $objectManager->create('\Magedelight\Looknbuy\Model\Lookitems')
                        ->getCollection()
                        ->addFieldToSelect('look_id')->addFieldToFilter('product_id', array('eq' => $product_id))->getData();

        $look_ids = array();
        foreach ($collections as $item) {
            $look_ids[] = $item['look_id'];
        }

        $lookCollection = $objectManager->create('\Magedelight\Looknbuy\Model\Looknbuy')
                ->getCollection()
                ->addFieldToSelect('*')->addFieldToFilter('status', array('eq' => 1))
                ->addFieldToFilter('look_id', array('in' => $look_ids));

        $this->setCollection($lookCollection);
    }

    public function _prepareLayout()
    {
        parent::_prepareLayout();

        return $this;

        //return parent::_prepareLayout();
    }

    public function imageResize($image)
    {
/*        $absPath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath().$image;
        $imageResized = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('resized/').$image;
        $imageResize = $this->_imageFactory->create();
        $imageResize->open($absPath);
        $imageResize->constrainOnly(true);
        $imageResize->keepTransparency(true);
        $imageResize->keepFrame(false);
        $imageResize->keepAspectRatio(true);
        $imageResize->resize(240, 300);
        $dest = $imageResized;
        $imageResize->save($dest);*/
        $resizedURL = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'resized/'.$image;

        return $resizedURL;
    }
}
