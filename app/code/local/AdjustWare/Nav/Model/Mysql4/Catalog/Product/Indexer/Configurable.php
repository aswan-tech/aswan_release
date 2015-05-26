<?php
/**
 * Product:     Layered Navigation Pro for Enterprise Edition - 16/08/12
 * Package:     AdjustWare_Nav_10.4.9_10.0.0_557110
 * Purchase ID: hXgQU3oI8FOfJ8PDCk5s6h6XPM5oH2Kx0N6TCAPkqN
 * Generated:   2013-04-22 06:59:44
 * File path:   app/code/local/AdjustWare/Nav/Model/Mysql4/Catalog/Product/Indexer/Configurable.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Nav')){ BfOpaChkEeNmajOB('d8873226c5a54e83b309ba237c0cf342'); ?><?php

class AdjustWare_Nav_Model_Mysql4_Catalog_Product_Indexer_Configurable extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Eav_Source
{
    /**
     * Flag that defines if need to disable keys during data inserting
     *
     * @var bool
     */
    protected $_isDisableKeys = true;

    /**
     * Initialize connection and define main table name
     */
    protected function _construct()
    {
        $this->_init('adjnav/catalog_product_index_configurable', 'entity_id');
    }

    /**
     * Rebuild index data by entities
     *
     * @param int|array $processIds
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Eav_Abstract
     */
    public function reindexEntities($processIds)
    {
        $adapter       = $this->_getWriteAdapter();
        $versionHelper = Mage::helper('adjnav/version');

        if ($versionHelper->isNewReindexAllMethod())
        {
            $this->clearTemporaryIndexTable();
        }
        else
        {
            $this->cloneIndexTable(true);
        }

        if (!is_array($processIds)) 
        {
            $processIds = array($processIds);
        }

        $parentIds = $this->getRelationsByChild($processIds);
        if ($parentIds) 
        {
            $processIds = array_unique(array_merge($processIds, $parentIds));
        }

        $processIds = $this->getRelationsByParent($processIds);

        $this->_prepareIndex($processIds);
        $this->_prepareRelationIndex($processIds);
        $this->_removeParentEntitiesFromIndex();

        $adapter->beginTransaction();
        try 
        {
            // remove old index
            $where = $adapter->quoteInto('entity_id IN(?)', $processIds);
            $adapter->delete($this->getMainTable(), $where);

            // insert new index
            $this->useDisableKeys(false);
            $this->insertFromTable($this->getIdxTable(), $this->getMainTable());
            $this->useDisableKeys(true);

            $adapter->commit();
        }
        catch (Exception $e) 
        {
            $adapter->rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * Rebuild all index data
     *
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Eav_Decimal
     */
    public function reindexAll()
    {
        if (Mage::helper('adjnav/version')->isNewReindexAllMethod())
        {
            $this->useIdxTable(true);
            $this->clearTemporaryIndexTable();
        }
        else
        {
            $this->cloneIndexTable(true);
        }

        $this->_prepareIndex();
        $this->_prepareRelationIndex();
        $this->_removeParentEntitiesFromIndex();

        $this->syncData();

        return $this;
    }

    protected function _removeParentEntitiesFromIndex()
    {
        $write      = $this->_getWriteAdapter();
        $idxTable   = $this->getIdxTable();

        $select = $write->select()
            ->from(array($idxTable), null)
            ->join(array('r' => $this->getTable('catalog/product_relation')), 'r.parent_id = '.$idxTable.'.entity_id', array());

        $query = $select->deleteFromSelect($idxTable);
        $write->query($query);

        return $this;
    }

    /**
     * Set or get flag that defines if need to disable keys during data inserting
     *
     * @param bool $value
     * @return Mage_Index_Model_Mysql4_Abstract
     */
    public function useDisableKeys($value = null)
    {
        if (!is_null($value)) {
            $this->_isDisableKeys = (bool)$value;
        }
        return $this->_isDisableKeys;
    }
} } 