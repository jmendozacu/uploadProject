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

namespace Magedelight\Looknbuy\Helper;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_GENERAL_ENABLED = 'bundlediscount/general/enable';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    protected $_customerSession;

    /**
     * Customer Group factory.
     *
     * @var \Magento\Customer\Model\GroupFactory
     */
    protected $_customerGroupFactory;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_catalogData;
    protected $_optionHelper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * Request object.
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\Url\EncoderInterface
     */
    protected $urlEncoder;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Helper\Context $urlContext
     */
    public function __construct(
    \Magento\Framework\App\Helper\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Filesystem $filesystem, \Magento\Framework\Image\AdapterFactory $imageFactory, \Magento\Framework\App\Helper\Context $urlContext
    ) {
        $this->_urlBuilder = $urlContext->getUrlBuilder();
        $this->_request = $urlContext->getRequest();
        $this->urlEncoder = $urlContext->getUrlEncoder();

        $this->_filesystem = $filesystem;

        $this->_storeManager = $storeManager;

        $this->_directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);

        $this->_imageFactory = $imageFactory;
        parent::__construct($context);
    }

    public function isEnabled()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_ENABLED, $storeScope);
    }

    public function formatPercentage($string)
    {
        preg_match('/^\d+\.\d+$/', $string, $matches);
        if (count($matches) > 0) {
            $parts = explode('.', $string);
            $firstPart = $parts[0];
            $decimalPart = $parts[1];
            $decimalDigits = str_split($decimalPart);
            if (!isset($decimalDigits[0])) {
                $decimalDigits[0] = 0;
            }
            if (!isset($decimalDigits[1])) {
                $decimalDigits[1] = 0;
            }
            if (!isset($decimalDigits[2])) {
                $decimalDigits[2] = 0;
            }
            if (!isset($decimalDigits[3])) {
                $decimalDigits[3] = 0;
            }

            $decimalDigits[1] = ($decimalDigits[2] > 5) ? $decimalDigits[1] + 1 : $decimalDigits[1];
            $convertdString = $firstPart;
            $convertdString .= ($decimalDigits[0] == '0' && $decimalDigits[1] == '0') ? '' : '.'.$decimalDigits[0];
            $convertdString .= ($decimalDigits[1] == '0') ? '' : $decimalDigits[1];

            return $convertdString;
        }

        return $string;
    }

    public function imageResize($image, $layout, $ishomepage = null )
    {
        $absPath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath().$image;
        $imageResized = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('look/resized/').$image;
        $imageResize = $this->_imageFactory->create();
        $imageResize->open($absPath);
        $imageResize->constrainOnly(false);
        $imageResize->keepTransparency(true);
        $imageResize->keepFrame(true);
        $imageResize->backgroundColor(array(255, 255, 255));
        $imageResize->keepAspectRatio(true);
        if ($ishomepage != "inspiration" ) {
            if ($layout == 1) {
//            $imageResize->resize(1240, 530);
//            $imageResize->resize(1240, 560);
//            $imageResize->resize(1125, 515);
            $imageResize->resize(1240, 530);
            } else {
                $imageResize->resize(620, 960);
            }    
        }else{
           if ($layout == 1) {
//            $imageResize->resize(1240, 530);
//            $imageResize->resize(1240, 560);
//            $imageResize->resize(1125, 515);
            $imageResize->resize(1240, 530);
            } else {
                $imageResize->resize(620, 960);
            }     
        }
        $dest = $imageResized;
        $imageResize->save($dest);
        $resizedURL = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'look/resized/'.$image;

        return $resizedURL;
    }

    public function getLookAddToCartUrl($lookId)
    {
        $routeParams = array(
            \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED => $this->urlEncoder->encode($this->_urlBuilder->getCurrentUrl()),
            '_secure' => $this->_request->isSecure(),
            'look_id' => $lookId,
        );

        return $this->_urlBuilder->getUrl('looknbuy/cart/add', $routeParams);
    }

    public function getDiscountLabel($storeId = null)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $label = (string) $this->scopeConfig->getValue('looknbuy/general/discount_label', $storeScope);

        if (is_null($label) || strlen($label) <= 0) {
            $label = $this->__('Look Discount');
        }

        return $label;
    }
}
