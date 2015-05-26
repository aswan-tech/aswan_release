<?php

$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();
 
$setup->addAttribute('catalog_category', 'category_code', array(
    'group'         => 'General Information',
    'input'         => 'text',
    'type'          => 'varchar',
    'label'         => 'Category Code',
    'backend'       => '',
    'visible'       => 1,
    'required'      => 1,
    'user_defined' => 1,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
 
$installer->endSetup();