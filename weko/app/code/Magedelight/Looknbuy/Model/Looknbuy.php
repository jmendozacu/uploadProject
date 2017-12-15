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

namespace Magedelight\Looknbuy\Model;

class Looknbuy extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @param \Magento\Framework\Model\Context                        $context
     * @param \Magento\Framework\Registry                             $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb           $resourceCollection
     * @param array                                                   $data
     */
    public function __construct(
    \Magento\Framework\Model\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null, \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    public function _construct()
    {
        $this->_init('Magedelight\Looknbuy\Model\ResourceModel\Looknbuy');
    }

    public function checkIdentifier($identifier, $storeId)
    {
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }

    public function hasOptions($look_id, $excludeSelection = false)
    {
        $hasOptions = false;
        $productTypes = array('grouped', 'configurable', 'bundle', 'downloadable');
        $products = $this->getProducts($look_id);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        if (!$hasOptions && !$excludeSelection) {
            foreach ($products as $_selection) {
                $product = $objectManager->create('Magento\Catalog\Model\Product')->load($_selection['pid']);

                if (in_array($product->getTypeId(), $productTypes)) {
                    $hasOptions = true;
                    break;
                }
            }
        }

        return $hasOptions;
    }

    public function hasCustomOptions($look_id, $excludeSelection = false)
    {
        $hasCustomOptions = false;
        $products = $this->getProducts($look_id);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        if (!$hasCustomOptions && !$excludeSelection) {
            foreach ($products as $_selection) {
                $product = $objectManager->create('Magento\Catalog\Model\Product')->load($_selection['pid']);

                if ($product->getHasCustomOptions() == 1) {
                    $hasCustomOptions = true;
                    break;
                }
            }
        }

        return $hasCustomOptions;
    }

    public function getProducts($look_id)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $optionCollection = $objectManager->create('\Magedelight\Looknbuy\Model\Lookitems')
                ->getCollection()
                ->addFieldToSelect('*')->addFieldToFilter('look_id', array('eq' => $look_id));

        $finaldata = array();
        $k = 0;
        $productObj = array();
        foreach ($optionCollection as $key => $option) {
            $finaldata[$key]['id'] = $key;

            $finaldata[$key]['pid'] = $option['product_id'];
            $finaldata[$key]['qty'] = $option['qty'];

            ++$k;
        }

        return $finaldata;
    }

    public function calculateProductQtys($lookIds)
    {
        if (!is_array($lookIds)) {
            $lookIds = array($lookIds);
        }
        $result = array();
        foreach ($lookIds as $id) {
            $look = $this->load($id);
            $selections = $this->getProducts($id);
            foreach ($selections as $_selection) {
                if (!isset($result[$_selection['pid']])) {
                    $result[$_selection['pid']][$id] = $_selection['qty'];
                } else {
                    $result[$_selection['pid']][$id] = $_selection['qty'];
                }
            }
        }

        return $result;
    }
}