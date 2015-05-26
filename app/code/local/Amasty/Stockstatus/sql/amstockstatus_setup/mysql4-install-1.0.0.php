<?php
/**
* @copyright Amasty.
*/

$installer = $this;

$installer->startSetup();

/**
* ADDING FIRST ATTRIBUTE
*/
$installer->addAttribute('catalog_product', 'custom_stock_status', array(
    'type'              => 'int',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Custom Stock Status',
    'input'             => 'select',
    'class'             => '',
    'source'            => 'eav/entity_attribute_source_table',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
    'apply_to'          => '',
    'is_configurable'   => false
));
$attributeId = $installer->getAttributeId('catalog_product', 'custom_stock_status');

foreach ($installer->getAllAttributeSetIds('catalog_product') as $attributeSetId) 
{
    try {
        $attributeGroupId = $installer->getAttributeGroupId('catalog_product', $attributeSetId, 'General');
    } catch (Exception $e) {
        $attributeGroupId = $installer->getDefaultAttributeGroupId('catalog_product', $attributeSetId);
    }
    $installer->addAttributeToSet('catalog_product', $attributeSetId, $attributeGroupId, $attributeId);
}

/**
* ADDING SECOND ATTRIBUTE
*/
$installer->addAttribute('catalog_product', 'hide_default_stock_status', array(
    'type'              => 'int',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Hide Default Stock Status',
    'input'             => 'select',
    'class'             => '',
    'source'            => 'eav/entity_attribute_source_boolean',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
    'apply_to'          => '',
    'is_configurable'   => false
));
$attributeId = $installer->getAttributeId('catalog_product', 'custom_stock_status');

foreach ($installer->getAllAttributeSetIds('catalog_product') as $attributeSetId) 
{
    try {
        $attributeGroupId = $installer->getAttributeGroupId('catalog_product', $attributeSetId, 'General');
    } catch (Exception $e) {
        $attributeGroupId = $installer->getDefaultAttributeGroupId('catalog_product', $attributeSetId);
    }
    $installer->addAttributeToSet('catalog_product', $attributeSetId, $attributeGroupId, $attributeId);
}


$installer->endSetup(); 