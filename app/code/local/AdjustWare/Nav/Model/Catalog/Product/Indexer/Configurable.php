<?php
/**
 * Product:     Layered Navigation Pro for Enterprise Edition - 16/08/12
 * Package:     AdjustWare_Nav_10.4.9_10.0.0_557110
 * Purchase ID: hXgQU3oI8FOfJ8PDCk5s6h6XPM5oH2Kx0N6TCAPkqN
 * Generated:   2013-04-22 06:59:44
 * File path:   app/code/local/AdjustWare/Nav/Model/Catalog/Product/Indexer/Configurable.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Nav')){ acZoMhqBerCjMkZa('2a801c08816c5c96a461cbcd3870c1b2'); ?><?php

class AdjustWare_Nav_Model_Catalog_Product_Indexer_Configurable extends Mage_Catalog_Model_Product_Indexer_Eav
{
    /**
     * Retrieve Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('adjnav')->__('Configurable Product Attributes');
    }

    /**
     * Retrieve Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('adjnav')->__('Index configurable product attributes for layered navigation pro filtering');
    }

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('adjnav/catalog_product_indexer_configurable');
    }
} } 