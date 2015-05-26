<?php
$installer = $this;
$installer->startSetup();
$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
$query = 'SELECT attribute_id FROM `' . $this->getTable('eav_attribute') . '` WHERE attribute_code="hover_image"';
$value = $connection->query($query);
$row = $value->fetch();
if(empty($row)){
$this->addAttribute(
    'catalog_product',
    'hover_image',
    array (
        'group'             => 'Images',
        'type'              => 'varchar',
        'frontend'          => 'catalog/product_attribute_frontend_image',
        'label'             => 'Hover Image',
        'input'             => 'media_image',
        'class'             => '',
        'source'            => '',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'           => '1',
        'required'          => '0',
        'user_defined'      => '1',
        'default'           => '',
        'searchable'        => '0',
        'filterable'        => '0',
        'comparable'        => '0',
        'visible_on_front'  => '1',
        'unique'            => '0',
    )
);

$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
$query = 'SELECT attribute_id FROM `' . $this->getTable('eav_attribute') . '` WHERE attribute_code="hover_image"';
$value = $connection->query($query);
$row = $value->fetch();
$attributeId = $row['attribute_id'];
$installer->run("UPDATE `{$installer->getTable('catalog_eav_attribute')}` SET `is_visible_on_front` = '1', `used_in_product_listing` = '1' WHERE attribute_id= ".$attributeId);
}
$installer->endSetup();