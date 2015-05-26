<?php
/**
* @copyright Amasty.
*/
$installer = $this;
$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS `{$this->getTable('amasty_stockstatus_quantityranges')}`  (
    `entity_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `qty_from` INT NOT NULL ,
    `qty_to` INT NOT NULL ,
    `status_id` INT UNSIGNED NOT NULL
) ENGINE = InnoDB ;
");

/**
* ADDING ATTRIBUTE FOR USE RANGES ON PRODUCT YES/NO
*/
$installer->addAttribute('catalog_product', 'custom_stock_status_qty_based', array(
    'type'              => 'int',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Use Quantity Ranges Based Stock Status',
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
$attributeId = $installer->getAttributeId('catalog_product', 'custom_stock_status_qty_based');

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