<?php

namespace ThinkIdeas\Categorysliders\Setup;

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
                \Magento\Catalog\Model\Category::ENTITY, 'homepage_cate_slider', [
            'type' => 'int',
            'label' => 'Display Slider in Home Page',
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

        $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY, 'catesliderpos', [
            'type' => 'varchar',
            'label' => 'Slider Position',
            'input' => 'text',
            'sort_order' => 333,
            'source' => '',
            'global' => 0,
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'default' => null,
            'group' => 'General Information',
            'backend' => ''
                ]
        );

        $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY, 'noofproductsinslider', [
            'type' => 'varchar',
            'label' => 'Numper of Products In slider',
            'input' => 'text',
            'sort_order' => 333,
            'source' => '',
            'global' => 0,
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'default' => null,
            'group' => 'General Information',
            'backend' => ''
                ]
        );
    }

}
