<?php

$installer = $this;
$setup = Mage::getResourceModel('catalog/setup', 'catalog_setup');
$installer->startSetup();

$setup->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'category_content_weburl', array(
    'group'         => 'General Information',
    'input'         => 'text',
    'type'          => 'varchar',
    'label'         => 'Web Url',
    'backend'       => '',
    'visible'       => 1,
    'required'      => 0,
    'user_defined' => 1,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$setup->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'category_content_imagemap', array(
    'group'         => 'General Information',
    'input'         => 'textarea',
    'type'          => 'text',
    'label'         => 'Map Image',
    'backend'       => '',
    'visible'       => 1,
    'required'      => 0,
    'user_defined' => 1,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$installer->endSetup();