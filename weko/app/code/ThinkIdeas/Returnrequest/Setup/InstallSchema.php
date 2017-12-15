<?php

namespace ThinkIdeas\Returnrequest\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface
{

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {

        $installer = $setup;

        $installer->startSetup();


        /**
         * Create table 'returnreqest'
         */
        $table = $installer->getConnection()
                ->newTable($installer->getTable('returnreqest'))
                ->addColumn(
                        'rr_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, [
                    'identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true
                        ], 'Retrun Record Id'
                )
                ->addColumn(
                        'order_no', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 12, [
                    'nullable' => false,
                        ], 'Order No'
                )
                ->addColumn(
                        'cust_name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 20, [
                    'nullable' => false,
                        ], 'Customer Name'
                )
                ->addColumn(
                        'rr_date', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 20, [
                    'nullable' => false,
                        ], 'Date'
                )
                ->addColumn(
                        'item_no', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
                    'nullable' => false,
                        ], 'Item No'
                )->addColumn(
                        'customer_email', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 50, [
                    'nullable' => false,
                        ], 'Customer Email'
                )
                ->addColumn(
                        'reason', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 150, [
                    'nullable' => false,
                        ], 'Reason'
                )
                ->addIndex(
                        $installer->getIdxName(
                                'returnreqest', ['rr_id']
                        ), ['rr_id']
                )
                ->setComment('returnreqest');
        $installer->getConnection()->createTable($table);


        $installer->endSetup();
    }

}
