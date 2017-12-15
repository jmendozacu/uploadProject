<?php
namespace ThinkIdeas\Customerattribute\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order_address'),
                'weko_card_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order_address'),
                'dob',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT
            );
        }

        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order'),
                'weko_card_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order'),
                'dob',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT
            );
        }

        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'weko_card_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'dob',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT
            );
        }

        $installer->endSetup();
    }
}
