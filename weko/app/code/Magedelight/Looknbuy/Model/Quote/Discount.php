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

namespace Magedelight\Looknbuy\Model\Quote;

class Discount extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    protected $quoteValidator = null;
    protected $_qtyArrays = array();

    /**
     * @param \Magento\Quote\Model\QuoteValidator $quoteValidator
     */
    public function __construct(\Magento\Quote\Model\QuoteValidator $quoteValidator)
    {
        $this->quoteValidator = $quoteValidator;
    }

    public function collect(
    \Magento\Quote\Model\Quote $quote, \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment, \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);
        $address = $shippingAssignment->getShipping()->getAddress();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $this->helper = $objectManager->create('Magedelight\Looknbuy\Helper\Data');
        $this->_priceModel = $objectManager->create('Magento\Catalog\Model\Product\Type\Price');

        $label = $this->helper->getDiscountLabel();
        $count = 0;
        $appliedCartDiscount = 0;
        $totalDiscountAmount = 0;
        $subtotalWithDiscount = 0;
        $baseTotalDiscountAmount = 0;
        $baseSubtotalWithDiscount = 0;

        $lookIds = explode(',', $quote->getData('look_ids'));
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        if ($lookIds[0] == '') {
            unset($lookIds[0]);
        }

        $this->_qtyArrays = $this->_objectManager->create('Magedelight\Looknbuy\Model\Looknbuy')->calculateProductQtys($lookIds);

        $items = $quote->getAllItems();

        if (!count($items)) {
            $address->setLookDiscountAmount($totalDiscountAmount);
            $address->setBaseLookDiscountAmount($baseTotalDiscountAmount);

            return $this;
        }

        $addressQtys = $this->_calculateAddressQtys($address);

        $finalLookIds = $this->_validateLookIds($addressQtys, $lookIds);
        if (is_array($addressQtys) && count($addressQtys) > 0) {
            $count += array_sum(array_values($addressQtys));
        }

        foreach ($finalLookIds as $id) {
            $look = $this->_objectManager->create('Magedelight\Looknbuy\Model\Looknbuy')->load($id);
            $excludeFromBaseProductFlag = ($look->getExcludeBaseProduct() == 0) ? false : true;
            $totalAmountOfLook = 0;
            $tempArray = array();
            foreach ($items as $item) {
                if ($item instanceof \Magento\Quote\Model\Quote\Address\Item) {
                    $quoteItem = $item->getAddress()->getQuote()->getItemById($item->getQuoteItemId());
                } else {
                    $quoteItem = $item;
                }
                $product = $quoteItem->getProduct();
                $product->setCustomerGroupId($quoteItem->getQuote()->getCustomerGroupId());
                if (isset($this->_qtyArrays[$quoteItem->getProduct()->getId()][$id])) {
                    if (!in_array($quoteItem->getProduct()->getId(), $tempArray)) {
                        if ($excludeFromBaseProductFlag && $product->getId() == $look->getProductId()) {
                            continue;
                        }
                        $tempArray[] = $quoteItem->getProduct()->getId();

                        $qty = $this->_qtyArrays[$quoteItem->getProduct()->getId()][$id];
                        $price = $quoteItem->getDiscountCalculationPrice();
                        $calcPrice = $quoteItem->getCalculationPrice();
                        $itemPrice = $price === null ? $calcPrice : $price;
                        $totalAmountOfLook += $itemPrice * $qty;
                    }
                }
            }

            if ($look->getDiscountType() == 1) {
                $totalDiscountAmount += $look->getDiscountPrice();

                $baseTotalDiscountAmount += $look->getDiscountPrice();
            } else {
                $totalDiscountAmount += ($look->getDiscountPrice() * $totalAmountOfLook) / 100;
                $baseTotalDiscountAmount += ($look->getDiscountPrice() * $totalAmountOfLook) / 100;
            }
        }

        $totalDiscountAmount = round($totalDiscountAmount, 2);
        $baseTotalDiscountAmount = round($baseTotalDiscountAmount, 2);

        $this->helper = $this->_objectManager->create('Magento\Tax\Helper\Data');

        $totaldata = $total->getData();

        $subTotal = $totaldata['subtotal'];
        $baseSubTotal = $totaldata['base_subtotal'];
        if ($totalDiscountAmount > 0 && $this->helper->applyTaxAfterDiscount()) {
            if ($count > 0) {
                $divided = $totalDiscountAmount / $count;
                $baseDivided = $baseTotalDiscountAmount / $count;
                foreach ($items as $item) {
                    $dividedItemDiscount = round(($item->getRowTotal() * $totalDiscountAmount) / $subTotal, 2);
                    $baseDividedItemDiscount = round(($item->getBaseRowTotal() * $baseTotalDiscountAmount) / $baseSubTotal, 2);

                    $oldDiscountAmount = $item->getDiscountAmount();
                    $oldBaseDiscountAmount = $item->getBaseDiscountAmount();
                    $origionalDiscountAmount = $item->getOriginalDiscountAmount();
                    $baseOrigionalDiscountAmount = $item->getBaseOriginalDiscountAmount();

                    $item->setDiscountAmount($oldDiscountAmount + $dividedItemDiscount);
                    $item->setBaseDiscountAmount($oldBaseDiscountAmount + $baseDividedItemDiscount);
                    $item->setOriginalDiscountAmount($origionalDiscountAmount + $dividedItemDiscount);
                    $item->setBaseOriginalDiscountAmount($baseOrigionalDiscountAmount + $baseDividedItemDiscount);
                }
            }
        }

        $address->setLookDiscountAmount($totalDiscountAmount);

        $address->setBaseLookDiscountAmount($baseTotalDiscountAmount);
        $quote->setLookDiscountAmount($totalDiscountAmount);
        $quote->setBaseLookDiscountAmount($baseTotalDiscountAmount);

        $discountAmount = -$totalDiscountAmount;

        if ($total->getDiscountDescription()) {
            // If a discount exists in cart and another discount is applied, the add both discounts.
            $appliedCartDiscount = $total->getDiscountAmount();
            $discountAmount = $total->getDiscountAmount() + $discountAmount;
            $label = $total->getDiscountDescription().', '.$label;
        }

        $getSubTotal = $total->getSubtotal();
        $tempDiscount = str_replace('-', '', $discountAmount);
        if ($tempDiscount > $getSubTotal) {
            $discountAmount = '-'.$getSubTotal;
        }

        $total->setDiscountDescription($label);
        $total->setDiscountAmount($discountAmount);
        $total->setBaseDiscountAmount($discountAmount);
        $total->setSubtotalWithDiscount($total->getSubtotal() + $discountAmount);
        $total->setBaseSubtotalWithDiscount($total->getBaseSubtotal() + $discountAmount);

        if (isset($appliedCartDiscount)) {
            $total->addTotalAmount($this->getCode(), $discountAmount - $appliedCartDiscount);
            $total->addBaseTotalAmount($this->getCode(), $discountAmount - $appliedCartDiscount);
        } else {
            $total->addTotalAmount($this->getCode(), $discountAmount);
            $total->addBaseTotalAmount($this->getCode(), $discountAmount);
        }

        return $this;
    }

    protected function _calculateAddressQtys(\Magento\Quote\Model\Quote\Address $address)
    {
        $result = array();
        $keys = array_keys($this->_qtyArrays);
        foreach ($address->getAllVisibleItems() as $item) {
            if (!isset($result[$item->getProductId()])) {
                $result[$item->getProductId()] = $item->getQty();
            } else {
                $result[$item->getProductId()] += $item->getQty();
            }
        }
        foreach ($keys as $productId) {
            if (!isset($result[$productId])) {
                $result[$productId] = 0;
            }
        }

        return $result;
    }

    protected function _validateLookIds($addressQtys, $lookIds)
    {
        $result = array();
        if (!is_array($lookIds)) {
            $lookIds = array($lookIds);
        }

        foreach ($lookIds as $lookId) {
            $isValid = true;
            foreach ($addressQtys as $productId => $qty) {
                if (isset($this->_qtyArrays[$productId][$lookId])) {
                    if ($this->_qtyArrays[$productId][$lookId] <= $qty) {
                        $addressQtys[$productId] -= $this->_qtyArrays[$productId][$lookId];
                    } else {
                        $isValid = false;
                    }
                }
            }
            if ($isValid) {
                $result[] = $lookId;
            }
        }

        return $result;
    }

    /**
     * Add discount total information to address.
     *
     * @param \Magento\Quote\Model\Quote               $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     *
     * @return array|null
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        $result = null;
        $amount = $total->getDiscountAmount();

        if ($amount != 0) {
            $description = $total->getDiscountDescription();
            $result = [
                'code' => $this->getCode(),
                'title' => strlen($description) ? __('Discount (%1)', $description) : __('Discount'),
                'value' => $amount,
            ];
        }

        return $result;
    }
}
