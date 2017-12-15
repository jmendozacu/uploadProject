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

namespace Magedelight\Looknbuy\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\InstallSchemaInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $table1 = $installer->getConnection()->newTable(
                        $installer->getTable('md_looks')
                )->addColumn(
                        'look_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'nullable' => false, 'primary' => true], 'Look Id'
                )->addColumn(
                        'look_name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '255', [], 'Name'
                )->addColumn(
                        'url_key', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '255', [], 'Url Key'
                )->addColumn(
                        'is_homepage', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, ['nullable' => false],
                            'Is Homepage banner'
                )->addColumn(
                        'category_ids', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, ['nullable' => false],
                            'Category IDs'
                )->addColumn(
                        'position_homepage', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '255', [], 'Position on Homepage'
                )->addColumn(
                        'base_image', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '255', [], 'Base Image'
                )->addColumn(
                        'discount_type', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, '5', ['unsigned' => true, 'nullable' => true, 'default' => 0], 'Discount Type'
                )->addColumn(
                        'discount_price', \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, '12,4', [], 'Discount Price'
                )->addColumn(
                        'status', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, '5', ['unsigned' => true, 'nullable' => true, 'default' => 1], 'Status'
                )->addColumn(
                        'layout', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, '5', ['unsigned' => true, 'nullable' => true, 'default' => 1], 'Status'
                )->addColumn(
                        'description', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '', [], 'Description'
                )->addColumn(
                        'meta_keywords', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '255', [], 'Meta Keywords'
                )->addColumn(
                        'meta_description', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '255', [], 'Deta Description'
                )->addColumn(
                        'markers', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '', [], 'Markers'
                );
//                ->addColumn(
//                        'sort_order', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, '5', ['unsigned' => true, 'nullable' => true, 'default' => 0], 'Sort Order'
//                );

        $installer->getConnection()->createTable($table1);

        $table2 = $installer->getConnection()->newTable(
                        $installer->getTable('md_look_items')
                )->addColumn(
                        'item_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'nullable' => false, 'primary' => true], 'Item Id'
                )->addColumn(
                        'look_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, '10', ['unsigned' => true, 'nullable' => true], 'Look ID'
                )->addColumn(
                        'product_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, '10', ['unsigned' => true, 'nullable' => true], 'Product ID'
                )->addColumn(
                        'product_name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '255', [], 'Product Name'
                )->addColumn(
                        'sku', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '255', [], 'Sku'
                )->addColumn(
                        'price', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '255', [], 'Price'
                )->addColumn(
                        'qty', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, '5', ['unsigned' => true, 'nullable' => true, 'default' => 1], 'Quantity'
                );
        $installer->getConnection()->createTable($table2);

        $installer->getConnection()->addColumn(
                $setup->getTable('quote'), 'look_ids', [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length' => '12,4',
            'nullable' => false,
            'comment' => 'Look ids',
                ]
        );

        $installer->getConnection()->addColumn(
                $setup->getTable('quote'), 'look_discount_amount', [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length' => 32,
            'nullable' => false,
            'default' => '0.0000',
            'comment' => 'Look Discount Amount',
                ]
        );

        $installer->getConnection()->addColumn(
                $setup->getTable('quote'), 'base_look_discount_amount', [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length' => 32,
            'nullable' => false,
            'default' => '0.0000',
            'comment' => 'Base Look Discount Amount',
                ]
        );

        $installer->getConnection()->addColumn(
                $setup->getTable('sales_order'), 'look_discount_amount', [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length' => 32,
            'nullable' => false,
            'default' => '0.0000',
            'comment' => 'Look Discount Amount',
                ]
        );

        $installer->getConnection()->addColumn(
                $setup->getTable('sales_order'), 'base_look_discount_amount', [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length' => 32,
            'nullable' => false,
            'default' => '0.0000',
            'comment' => 'Base Look Discount Amount',
                ]
        );

        $installer->getConnection()->addColumn(
                $setup->getTable('sales_invoice'), 'look_discount_amount', [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length' => 32,
            'nullable' => false,
            'default' => '0.0000',
            'comment' => 'Look Discount Amount',
                ]
        );

        $installer->getConnection()->addColumn(
                $setup->getTable('sales_invoice'), 'base_look_discount_amount', [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length' => 32,
            'nullable' => false,
            'default' => '0.0000',
            'comment' => 'Base Look Discount Amount',
                ]
        );

        $installer->getConnection()->addColumn(
                $setup->getTable('sales_creditmemo'), 'look_discount_amount', [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length' => 32,
            'nullable' => false,
            'default' => '0.0000',
            'comment' => 'Look Discount Amount',
                ]
        );

        $installer->getConnection()->addColumn(
                $setup->getTable('sales_creditmemo'), 'base_look_discount_amount', [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length' => 32,
            'nullable' => false,
            'default' => '0.0000',
            'comment' => 'Base Look Discount Amount',
                ]
        );

        $connection = $installer->getConnection();

        $installer->endSetup();
    }
}
