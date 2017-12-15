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

namespace Magedelight\Looknbuy\Model\ResourceModel;

class Looknbuy extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function _construct()
    {
        $this->_init('md_looks', 'look_id');
    }

    /**
     * Check if page identifier exist for specific store
     * return page id if page exists.
     *
     * @param string $identifier
     * @param int    $storeId
     *
     * @return int
     */
    public function checkIdentifier($identifier, $storeId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $this->scopeConfig = $objectManager->create('\Magento\Framework\App\Config\ScopeConfigInterface');
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $suffix = trim($this->scopeConfig->getValue('looknbuy/general/url_suffix', $storeScope), '/');
        $identifier = rtrim($identifier, $suffix);

        $stores = [\Magento\Store\Model\Store::DEFAULT_STORE_ID, $storeId];
        $select = $this->_getLoadByIdentifierSelect($identifier, $stores, 1);
        $select->reset(\Magento\Framework\DB\Select::COLUMNS)->columns('cp.look_id')->limit(1);

        return $this->getConnection()->fetchOne($select);
    }

    /**
     * Retrieve load select with filter by identifier, store and activity.
     *
     * @param string    $identifier
     * @param int|array $store
     * @param int       $isActive
     *
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadByIdentifierSelect($identifier, $store, $isActive = null)
    {
        $select = $this->getConnection()->select()->from(
            ['cp' => $this->getMainTable()]
        )->where(
            'cp.url_key= ?',
            $identifier
        );

        if (!is_null($isActive)) {
            $select->where('cp.status = ?', $isActive);
        }

        return $select;
    }
}
