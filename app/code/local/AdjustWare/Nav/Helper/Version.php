<?php
/**
 * Product:     Layered Navigation Pro for Enterprise Edition - 16/08/12
 * Package:     AdjustWare_Nav_10.4.9_10.0.0_557110
 * Purchase ID: hXgQU3oI8FOfJ8PDCk5s6h6XPM5oH2Kx0N6TCAPkqN
 * Generated:   2013-04-22 06:59:44
 * File path:   app/code/local/AdjustWare/Nav/Helper/Version.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Nav')){ acZoMhqBerCjMkZa('97b148935efe848629271020b5de4f56'); ?><?php

/**
 * 
 * @author ksenevich@aitoc.com
 */
class AdjustWare_Nav_Helper_Version extends Mage_Core_Helper_Abstract
{
    public function getProductIdChildColumn()
    {
        if (version_compare(Mage::getVersion(), '1.9') >= 0)
        {
            return 'child_id';
        }

        return 'product_id';
    }

    public function getProductRelationTable()
    {
        if (version_compare(Mage::getVersion(), '1.9') >= 0)
        {
            return 'catalog/product_relation';
        }

        return 'catalog/product_super_link';
    }
    
    public function getBaseIndexTable()
    {
        if (version_compare(Mage::getVersion(), '1.9') >= 0)
        {
            return 'catalog_product_index_eav';
        }

        return 'catalogindex_eav';
    }

    /** Configurable fix works with 1.4+ versions only 
     * 
     * @return boolean
     */
    public function hasConfigurableFix()
    {
        return (boolean)(version_compare(Mage::getVersion(), '1.9') >= 0);
    }

    /**
     * 
     * @return boolean
     */
    public function isNewReindexAllMethod()
    {
        return (boolean)(version_compare(Mage::getVersion(), '1.9') >= 0);
    }
    
    public function getVersionSkinJs()
    {
        $version = '13';
        if (version_compare(Mage::getVersion(), '1.4.0.0', '>='))
        {
            $version = '14';
        }

        return 'js/adjnav-'.$version.'.js';
    }
} } 