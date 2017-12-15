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

namespace Magedelight\Looknbuy\Controller;

class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * Response.
     *
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\App\ActionFactory               $actionFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\ResponseInterface           $response
     */
    public function __construct(
    \Magento\Framework\App\ActionFactory $actionFactory,
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
            \Magedelight\Looknbuy\Model\LooknbuyFactory $pageFactory,
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            \Magento\Framework\App\ResponseInterface $response
    ) {
        $this->actionFactory = $actionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->_pageFactory = $pageFactory;
        $this->_storeManager = $storeManager;
        $this->_response = $response;
    }

    /**
     * Validate and Match.
     *
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $urlKey = trim($this->scopeConfig->getValue('looknbuy/general/url_key', $storeScope), '/');
        $suffix = trim($this->scopeConfig->getValue('looknbuy/general/url_suffix', $storeScope), '/');
        $urlKey .= (strlen($suffix) > 0 || $suffix != '') ? '.'.str_replace('.', '', $suffix) : '';

        $identifier = trim($request->getPathInfo(), '/');
        if ($identifier == $urlKey) {
            $request->setModuleName('looknbuy')->setControllerName('index')->setActionName('looks');
            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
        } else {
            $condition = new \Magento\Framework\DataObject(['identifier' => $identifier, 'continue' => true]);
            $identifier = $condition->getIdentifier();

            $page = $this->_pageFactory->create();
            $lookId = $page->checkIdentifier($identifier, $this->_storeManager->getStore()->getId());
            if (!$lookId) {
                return;
            }
            $request->setModuleName('looknbuy')->setControllerName('index')->setActionName('view')->setParam('look_id', $lookId);
            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);

            //return false;
        }

        return $this->actionFactory->create(
                        'Magento\Framework\App\Action\Forward', ['request' => $request]
        );
    }
}
