<?php
/**
 * @category     Inchoo
 * @package     Inchoo Seller Products
 * @author        Domagoj Potkoc, Inchoo Team <web@inchoo.net>
 * @modified    Mladen Lotar <mladen.lotar@surgeworks.com>, Vedran Subotic <vedran.subotic@surgeworks.com>
 */

$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();


$installer->addAttribute('catalog_product', 'inchoo_seller_product', array(
        'group'             => 'General',
        'type'              => 'int',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Seller product',
        'input'             => 'boolean',
        'class'             => '',
        'source'            => '',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'           => true,
        'required'          => false,
        'user_defined'      => true,
        'default'           => '0',
        'searchable'        => false,
        'filterable'        => false,
        'comparable'        => false,
        'visible_on_front'  => false,
        'unique'            => false,
        'apply_to'          => 'simple,configurable,virtual,bundle,downloadable',
        'is_configurable'   => false
    ));

$installer->endSetup();
