<?php

$installer = $this;
$setup = Mage::getResourceModel('catalog/setup', 'catalog_setup');
$installer->startSetup();

$setup->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'category_content_heading', array(
    'group'         => 'General Information',
    'input'         => 'text',
    'type'          => 'varchar',
    'label'         => 'Heading',
    'backend'       => '',
    'visible'       => 1,
    'required'      => 0,
    'user_defined' => 1,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$setup->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'category_content_sub_heading', array(
    'group'         => 'General Information',
    'input'         => 'text',
    'type'          => 'varchar',
    'label'         => 'Sub-Heading',
    'backend'       => '',
    'visible'       => 1,
    'required'      => 0,
    'user_defined' => 1,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$installer->endSetup();