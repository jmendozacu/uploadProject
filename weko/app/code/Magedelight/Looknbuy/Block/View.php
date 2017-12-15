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

class View extends \Magento\Framework\View\Element\Template
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
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlHelper;

    /**
     * @var \Magento\Checkout\Helper\Cart
     */
    protected $_cartHelper;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Customer\Model\Session        $customerSession
     * @param \Magento\Catalog\Model\ProductFactory  $productFactory
     * @param \Magento\Framework\Url\Helper\Data     $urlHelper
     * @param array                                  $data
     */
    public function __construct(
    \Magento\Catalog\Block\Product\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Catalog\Model\ProductFactory $productFactory, \Magento\Framework\Url\Helper\Data $urlHelper, array $data = []
    ) {
        $this->imageBuilder = $context->getImageBuilder();
        $this->scopeConfig = $context->getScopeConfig();
        $this->customerSession = $customerSession;
        $this->_productFactory = $productFactory;
        $this->urlHelper = $urlHelper;
        parent::__construct($context, $data);
    }

    public function getLook()
    {
        $look_id = $this->getRequest()->getParam('look_id');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $look = $objectManager->create('\Magedelight\Looknbuy\Model\Looknbuy')->load($look_id);

        return $look;
    }


    public function getBannerLook()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $look = $objectManager->create('\Magedelight\Looknbuy\Model\Looknbuy')->getCollection()
            ->addFieldToSelect('*')->addFieldToFilter('status', array('eq' => 1))
                        ->addFieldToFilter('is_homepage', array('eq' => 1));

        return $look;
    }

    public function getCategoryBannerLook()
    {
        $catId = $this->getRequest()->getParam('id');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $look = $objectManager->create('\Magedelight\Looknbuy\Model\Looknbuy')->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('status', array('eq' => 1))
            ->addFieldToFilter('is_homepage', array('eq' => 2))
            ->addFieldToFilter('category_ids', array('finset' => $catId));

        // echo"<pre/>"; print_r($look->getSelect()->__toString());exit;
        return $look;
    }

    public function getProducts()
    {
        $lookId = $this->getRequest()->getParam('look_id');

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $optionCollection = $objectManager->create('\Magedelight\Looknbuy\Model\Lookitems')
                        ->getCollection()
                        ->addFieldToSelect('*')->addFieldToFilter('look_id', array('eq' => $lookId));

        $finaldata = array();
        $k = 0;
        $productObj = array();
        foreach ($optionCollection as $key => $option) {
            $finaldata[$key]['id'] = $key;
            $finaldata[$key]['pname'] = htmlspecialchars($option['product_name']);
            $finaldata[$key]['pid'] = $option['product_id'];
            $finaldata[$key]['psku'] = $option['sku'];
            $finaldata[$key]['price'] = $option['price'];
            $finaldata[$key]['qty'] = $option['qty'];

            ++$k;
        }

        return json_encode($finaldata);
    }

    public function getProductPriceHtml(
    \Magento\Catalog\Model\Product $product, $priceType, $renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST, array $arguments = []
    ) {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }

        /** @var \Magento\Framework\Pricing\Render $priceRender */
        $priceRender = $this->getLayout()->createBlock('Magento\Framework\Pricing\Render', '', ['data' => ['price_render_handle' => 'catalog_product_prices']]);
        $price = '';

        if ($priceRender) {
            $price = $priceRender->render(
                    $priceType, $product, $arguments
            );
        }

        return $price;
    }

    public function getOptionsHtml(\Magento\Catalog\Model\Product $product)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $optionHtmlRenderer = $this->getLayout()->createBlock('Magento\Catalog\Block\Product\View', '');

        $calender = $this->getLayout()->createBlock('Magento\Framework\View\Element\Html\Calendar', '')->setTemplate('Magento_Theme::js/calendar.phtml');
        $optionRenderer = $this->getLayout()->createBlock('Magedelight\Looknbuy\Block\Product\View\Options', '');

        if ($optionRenderer) {
            $optionRenderer->setProduct($product);
            $optionHtmlRenderer->setProductId($product->getId());
            $defaultBlock = $this->getLayout()->createBlock('Magedelight\Looknbuy\Block\Product\View\Options\Type\DefaultType', '')->setTemplate('Magento_Catalog::product/view/options/type/default.phtml');
            $textBlock = $this->getLayout()->createBlock('Magedelight\Looknbuy\Block\Product\View\Options\Type\Text', '')->setTemplate('Magento_Catalog::product/view/options/type/text.phtml');
            $fileBlock = $this->getLayout()->createBlock('Magedelight\Looknbuy\Block\Product\View\Options\Type\File', '')->setTemplate('Magento_Catalog::product/view/options/type/file.phtml');
            $selectBlock = $this->getLayout()->createBlock('Magedelight\Looknbuy\Block\Product\View\Options\Type\Select', '')->setTemplate('Magento_Catalog::product/view/options/type/select.phtml');
            $dateBlock = $this->getLayout()->createBlock('Magedelight\Looknbuy\Block\Product\View\Options\Type\Date', '')->setTemplate('Magento_Catalog::product/view/options/type/date.phtml');

            $optionRenderer->setChild('default', $defaultBlock);
            $optionRenderer->setChild('text', $textBlock);
            $optionRenderer->setChild('file', $fileBlock);
            $optionRenderer->setChild('select', $selectBlock);
            $optionRenderer->setChild('date', $dateBlock);

            $optionRenderer->setTemplate('Magedelight_Looknbuy::product/view/options.phtml');

            $optionHtmlRenderer->setChild('product_options', $optionRenderer);
            $optionHtmlRenderer->setChild('html_calendar', $calender);

            $optionHtmlRenderer->setTemplate('Magento_Catalog::product/view/options/wrapper.phtml');

            return $optionHtmlRenderer->toHtml();
        }

        return '';
    }

    public function getImageUrl($image)
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

    public function getBundleRenderer()
    {
        $this->_bundleRenderer = [
            [
                'type' => 'select',
                'block' => 'Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option\Select',
            ], [
                'type' => 'multi',
                'block' => 'Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option\Multi',
            ], [
                'type' => 'radio',
                'block' => 'Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option\Radio',
            ], [
                'type' => 'checkbox',
                'block' => 'Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option\Checkbox',
            ],
        ];

        return $this->_bundleRenderer;
    }

    public function calculateDiscountAmount($look)
    {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_helper = $this->_objectManager->create('Magedelight\Looknbuy\Helper\Data');
        $priceHelper = $this->_objectManager->create('Magento\Framework\Pricing\Helper\Data');

        if ($look->getDiscountType() == 1) {
            $discount = $priceHelper->currency($look->getDiscountPrice(), true, false);
        } else {
            $discount = $this->_helper->formatPercentage($look->getDiscountPrice()).'%';
        }

        return $discount;
    }
}
