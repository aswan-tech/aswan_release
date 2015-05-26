<?php
/**
 * Product:     Layered Navigation Pro for Enterprise Edition - 16/08/12
 * Package:     AdjustWare_Nav_10.4.9_10.0.0_557110
 * Purchase ID: hXgQU3oI8FOfJ8PDCk5s6h6XPM5oH2Kx0N6TCAPkqN
 * Generated:   2013-04-22 06:59:44
 * File path:   app/code/local/AdjustWare/Nav/Model/Rewrite/CatalogResourceEavMysql4ProductIndexerEav.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Nav')){ mREwjpoZaMTrjgEm('ab11072a87cb1bef7a31ab4fae2a0470'); ?><?php

class AdjustWare_Nav_Model_Rewrite_CatalogResourceEavMysql4ProductIndexerEav extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Eav
{
    protected function _construct()
    {
        parent::_construct();

        $this->getIndexers();
        $this->_types['configurable'] = Mage::getResourceModel('adjnav/catalog_product_indexer_configurable');
    }
} } 