<?php
namespace ThinkIdeas\Customerattribute\Setup;

use Magento\Customer\Model\Customer;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
 
class UpgradeData implements UpgradeDataInterface{

    protected $eavSetupFactory;

    public function __construct(
            \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
            \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
        )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->customerSetupFactory = $customerSetupFactory;
    }

     public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if ($context->getVersion()
            && version_compare($context->getVersion(), '1.0.2') < 0
        ) {
            $eavSetup = $this->eavSetupFactory->create();

            $entityTypeId = 2; // Find these in the eav_entity_type table
            $eavSetup->removeAttribute($entityTypeId, 'weko_card_number');
            $eavSetup->removeAttribute($entityTypeId, 'dob');
        }
        if ($context->getVersion()
            && version_compare($context->getVersion(), '1.0.3') < 0
        ) {
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
            /* weko card number attribute*/
            $customerSetup->addAttribute('customer', 'weko_card_number', [
                'type' => 'varchar',
                'label' => 'Weko Card Number',
                'input' => 'text',
                'source' => '',
                'required' => false,
                'visible' => true,
                'position' => 333,
                'system' => false,
                'backend' => ''
            ]);            
            $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'weko_card_number')
                ->addData(['used_in_forms' => [
                    'adminhtml_customer',
                    'customer_account_edit'
                ]]);
            $attribute->save();

            /* dob attribute */
            $customerSetup->addAttribute('customer', 'dob', [
            'label' => 'Date Of Birth',
            'input' => 'date',
            'type' => 'datetime',
            'source' => '',
            'required' => false,
            'position' => 200,
            'visible' => true,
            'system' => false,
            'is_used_in_grid' => false,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => false,
            'is_searchable_in_grid' => false,
            'backend' => ''
        ]);        
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'dob')
            ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'customer_account_edit'
            ]]);
        $attribute->save();

        }

        $setup->endSetup();

    }
}