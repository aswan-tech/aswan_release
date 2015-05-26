<?php
/**
 * Product:     Layered Navigation Pro for Enterprise Edition - 16/08/12
 * Package:     AdjustWare_Nav_10.4.9_10.0.0_557110
 * Purchase ID: hXgQU3oI8FOfJ8PDCk5s6h6XPM5oH2Kx0N6TCAPkqN
 * Generated:   2013-04-22 06:59:44
 * File path:   app/code/local/AdjustWare/Nav/Model/Rewrite/CatalogIndexMysql4Attribute.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Nav')){ BfOpaChkEeNmajOB('4d0094c17a575be80166cd99f5578ddb'); ?><?php

/**
 * @author ksenevich@aitoc.com
 */
class AdjustWare_Nav_Model_Rewrite_CatalogIndexMysql4Attribute extends Mage_CatalogIndex_Model_Mysql4_Attribute
{
    /** Separates counts' calculation for configurable and other products  
     * 
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param Varien_Db_Select $select
     */
    public function getCount($attribute, $entitySelect)
    {
        $select = clone $entitySelect;
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);

        $fields = array(
        	'count'   => 'COUNT(index.entity_id)', 
        	'index.value',
            'type_id' => '("other")', 
            );

        $select->columns($fields)
            ->join(array('index' => $this->getMainTable()), 'index.entity_id = e.entity_id', array())
            ->where('index.store_id = ?', $this->getStoreId())
            ->where('index.attribute_id = ?', $attribute->getId())
            ->group('index.value');

        $configurableSelect = $this->_getCountForConfigurable($attribute, $select, $entitySelect);

        $select = '('.$select->__toString().')';
        if ($configurableSelect)
        {
            $select = 
            	' SELECT SUM(t.count) AS count, t.value '.
            	' FROM ('.$select.' UNION ('.$configurableSelect.')) AS t '.
            	' GROUP BY t.value ';
        }

        $result = $this->_getReadAdapter()->fetchAll($select);

        $counts = array();
        foreach ($result as $row) 
        {
            $counts[$row['value']] = $row['count'];
        }

        return $counts;
    }

    /** Get counts from index by configurable attributes if applicable
     * 
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param Varien_Db_Select $select
     */
    protected function _getCountForConfigurable($attribute, Varien_Db_Select $select, Varien_Db_Select $baseSelect)
    {
        /* @var $versionHelper AdjustWare_Nav_Helper_Version */
        $versionHelper = Mage::helper('adjnav/version');

        if (!$versionHelper->hasConfigurableFix())
        {
            return false;
        }

        /* @var $configurableSelect Varien_Db_Select */
        $configurableSelect = clone $baseSelect;
        $configurableSelect->reset(Zend_Db_Select::COLUMNS);
        $configurableSelect->reset(Zend_Db_Select::ORDER);
        $configurableSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $configurableSelect->reset(Zend_Db_Select::LIMIT_OFFSET);

        $configurableFrom   = $configurableSelect->getPart(Zend_Db_Select::FROM);
        $attributeJoins     = array();
        foreach ($configurableFrom as $alias => $tableInfo)
        {
            if (0 === strpos($alias, 'attr_index_'))
            {
                $tableInfo['tableName']     = $this->getTable('adjnav/catalog_product_index_configurable');
                $tableInfo['joinCondition'] = str_replace('e.entity_id', 'l.'.$versionHelper->getProductIdChildColumn(), $tableInfo['joinCondition']);

                $attributeJoins[$alias] = $tableInfo;
                unset($configurableFrom[$alias]);
            }
        }

        if (count($attributeJoins))
        {
            $configurableSelect->setPart(Zend_Db_Select::FROM, $configurableFrom);
        }

        $configurableSelect->join(
            array('l' => $this->getTable($versionHelper->getProductRelationTable())), 
            'e.entity_id = l.parent_id', 
            array()
            );

        $configurableFrom = $configurableSelect->getPart(Zend_Db_Select::FROM);

        foreach ($attributeJoins as $alias => $tableInfo)
        {
            $configurableFrom[$alias] = $tableInfo;
        }

        $configurableSelect->setPart(Zend_Db_Select::FROM, $configurableFrom);

        $select->where('e.type_id != ?', Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE);

        $fields = array(
        	'count' => 'COUNT(DISTINCT(e.entity_id))', 
        	'index.value', 
            'type_id' => '("configurable")', 
            );

        $configurableSelect->columns($fields)
            ->join(array('index' => $this->getTable('adjnav/catalog_product_index_configurable')), 'index.entity_id = l.'.$versionHelper->getProductIdChildColumn(), array())
            ->where('index.store_id = ?', $this->getStoreId())
            ->where('index.attribute_id = ?', $attribute->getId())
            ->where('e.type_id = ?', Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE)
            ->group('index.value');

        return $configurableSelect;
    }
} } 