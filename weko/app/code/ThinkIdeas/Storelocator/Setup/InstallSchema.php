<?php
declare(strict_types=1);
/**
 * ThinkIdeas_Storelocator extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  ThinkIdeas
 * @package   ThinkIdeas_Storelocator
 * @copyright 2016 Claudiu Creanga
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Claudiu Creanga
 */
 
namespace ThinkIdeas\Storelocator\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.Generic.CodeAnalysis.UnusedFunctionParameter)
     */
     
    // @codingStandardsIgnoreStart
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    
    // @codingStandardsIgnoreEnd
    {
        $installer = $setup;
        $installer->startSetup();
        if (!$installer->tableExists('thinkideas_storelocator_stores')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('thinkideas_storelocator_stores'));
            $table->addColumn(
                    'stockist_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'Store ID'
                )
                ->addColumn(
                    'store_id',
                    Table::TYPE_SMALLINT,
                    null,
                    [
                        'unsigned'  => true,
                        'nullable'  => false,
                        'primary'   => true,
                    ],
                    'Store ID'
                )
                ->addColumn(
                    'name',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable'  => false,],
                    'Store Name'
                )
                ->addColumn(
                    'address',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable'  => false,],
                    'Store Address'
                )
                ->addColumn(
                    'city',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'City'
                )
                ->addColumn(
                    'country',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Country'
                )
                ->addColumn(
                    'postcode',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Postcode'
                )
                ->addColumn(
                    'region',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Region'
                )
                ->addColumn(
                    'mondaytofriday',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Monday to Friday Schedule'
                )
                ->addColumn(
                    'saturday',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Saturday Schedule'
                )
                ->addColumn(
                    'holiday',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Holiday Schedule'
                )
                ->addColumn(
                    'newyear',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'New Year Schedule'
                )
                ->addColumn(
                    'email',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Email'
                )
                ->addColumn(
                    'phone',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Phone'
                )
                ->addColumn(
                    'iln_number',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'ILN Number'
                )
                ->addColumn(
                    'link',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Link'
                )
                ->addColumn(
                    'image',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Image'
                )
                ->addColumn(
                    'latitude',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Latitude'
                )
                ->addColumn(
                    'longitude',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Longitude'
                )
                ->addColumn(
                    'status',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'nullable'  => false,
                        'default'   => '1',
                    ],
                    'Status'
                )
                ->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    [],
                    'Update at'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    [],
                    'Creation Time'
                )
                ->setComment('List of stores');
                
            $installer->getConnection()->createTable($table);
            
            $installer->getConnection()->addIndex(
                $installer->getTable('thinkideas_storelocator_stores'),
                $setup->getIdxName(
                    $installer->getTable('thinkideas_storelocator_stores'),
                    ['name','photo'],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                [
                    'name',
                    'address',
                    'city',
                    'country',
                    'region',
                    'link',
                    'email',
                    'postcode',
                    'phone',
                    'latitude',
                    'longitude'
                ],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            );
            

            if (!$installer->tableExists('thinkideas_storelocator_products')) {
            $table = $installer->getConnection()->newTable($installer->getTable('thinkideas_storelocator_products')
                )->addColumn(
                    'stockist_id', 
                    Table::TYPE_INTEGER, 
                    10, 
                    ['nullable' => false, 'unsigned' => true]
                )->addColumn(
                    'product_id', 
                    Table::TYPE_INTEGER, 
                    10, 
                    ['nullable' => false, 'unsigned' => true], 
                    'Magento Product Id'
                )->addColumn(
                    'product_quantity', 
                    Table::TYPE_INTEGER, 
                    20, 
                    ['nullable' => false, 'unsigned' => true], 
                    'Magento Product Quantity'
                )->addForeignKey(
                    $installer->getFkName(
                        'thinkideas_storelocator_stores',
                        'stockist_id',
                        'thinkideas_storelocator_products',
                        'stockist_id'
                    ),
                    'stockist_id',
                    $installer->getTable('thinkideas_storelocator_stores'),
                    'stockist_id',
                    Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $installer->getFkName(
                        'thinkideas_storelocator_products',
                        'stockist_id',
                        'catalog_product_entity',
                        'entity_id'
                    ),
                    'product_id',
                    $installer->getTable('catalog_product_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE
                )
                ->setComment('Thinkideas Stores related Product relation table');

            $installer->getConnection()->createTable($table);
        }


            $installer->endSetup();
            
        }
    }
}