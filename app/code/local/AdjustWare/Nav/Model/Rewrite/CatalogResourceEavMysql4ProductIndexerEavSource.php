<?php
/**
 * Product:     Layered Navigation Pro for Enterprise Edition - 16/08/12
 * Package:     AdjustWare_Nav_10.4.9_10.0.0_557110
 * Purchase ID: hXgQU3oI8FOfJ8PDCk5s6h6XPM5oH2Kx0N6TCAPkqN
 * Generated:   2013-04-22 06:59:44
 * File path:   app/code/local/AdjustWare/Nav/Model/Rewrite/CatalogResourceEavMysql4ProductIndexerEavSource.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Nav')){ MQPiDqpargRkDBPM('9e9d52dac36b0f863ed0d05ea965e9cf'); ?><?php

class AdjustWare_Nav_Model_Rewrite_CatalogResourceEavMysql4ProductIndexerEavSource extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Eav_Source
{
    /** Reindex updated product children also with configurable update
     * 
     * @author ksenevich@aitoc.com
     */
    public function reindexEntities($processIds)
    {
        if (!is_array($processIds)) 
        {
            $processIds = array($processIds);
        }

        $childIds = $this->getRelationsByParent($processIds);
        if ($childIds) 
        {
            $processIds = array_unique(array_merge($processIds, $childIds));
        }

        return parent::reindexEntities($processIds);
    }
} } 