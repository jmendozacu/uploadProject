<?php

namespace ThinkIdeas\Additional\Setup;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class InstallData implements InstallDataInterface
{
    /** @var \Magento\Eav\Setup\EavSetupFactory
      
     */
private $eavSetupFactory;
    /**

      /**
     * Constructor
     *
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(
        ModuleDataSetupInterface $setup, ModuleContextInterface $context
    )
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY, 'category_product_count', [
            'type' => 'int',
            'label' => 'Display product count',
            'input' => 'select',
            'sort_order' => 333,
            'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
            'global' => 0,
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'default' => 0,
            'group' => 'General Information',
            'backend' => ''
                ]
        );

    }

}
