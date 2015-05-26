<?php
/**
 * Product:     Layered Navigation Pro for Enterprise Edition - 16/08/12
 * Package:     AdjustWare_Nav_10.4.9_10.0.0_557110
 * Purchase ID: hXgQU3oI8FOfJ8PDCk5s6h6XPM5oH2Kx0N6TCAPkqN
 * Generated:   2013-04-22 06:59:44
 * File path:   app/code/local/AdjustWare/Nav/Model/Mysql4/Eav/Entity/Attribute/Option/Stat.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Nav')){ rIapgwhejkcDgyar('bb47802cf7927145865aa1ea52630049'); ?><?php

/**
 * 
 * @author ksenevich
 */
class AdjustWare_Nav_Model_Mysql4_Eav_Entity_Attribute_Option_Stat extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('adjnav/eav_attribute_option_stat', 'option_id');
    }

    public function addStat($optionIds)
    {
        if (empty($optionIds))
        {
            return $this;
        }

        if (!is_array($optionIds))
        {
            $optionIds = array($optionIds);
        }

        $write = $this->_getWriteAdapter();

        $sql = 
        	' INSERT INTO `'.$this->getTable('eav_attribute_option_hit').'` (option_id) '.
        	' VALUES ('.join('),(', array_map('intval', $optionIds)).') ';
        $write->query($sql);
        //$write->insertArray($this->getTable('eav_attribute_option_hit'), array('option_id'), $optionIds);

        return $this;
    }

    public function recalculateStat()
    {
        $write                  = $this->_getWriteAdapter();
        $tmpTable               = 'adjnav_tmp_'.md5(uniqid(mt_rand(), true));
        $optionHitReplaceTable  = 'adjnav_option_hit_replace';
        $optionStatReplaceTable = 'adjnav_option_stat_replace';
        $attrStatReplaceTable   = 'adjnav_attr_stat_replace';
        $write->query('DROP TABLE IF EXISTS `'.$optionHitReplaceTable.'`');
        $write->query('CREATE TABLE `'.$optionHitReplaceTable.'` LIKE `'.$this->getTable('eav_attribute_option_hit').'`');
        $write->query(
            ' RENAME TABLE `'.$this->getTable('eav_attribute_option_hit').'` TO `'.$tmpTable.'` '.
            ' , `'.$optionHitReplaceTable.'` TO `'.$this->getTable('eav_attribute_option_hit').'` '.
            ' , `'.$tmpTable.'` TO `'.$optionHitReplaceTable.'` '
            );

        // Collect attributes' values stats
        $write->query('DROP TABLE IF EXISTS `'.$optionStatReplaceTable.'`');
        $write->query('CREATE TABLE `'.$optionStatReplaceTable.'` LIKE `'.$this->getTable('eav_attribute_option_stat').'`');
        $write->query(
            ' INSERT INTO `'.$optionStatReplaceTable.'` '.
            ' SELECT * FROM `'.$this->getTable('eav_attribute_option_stat').'` '
            );

        $baseSelect    = new Varien_Db_Select($this->getReadConnection());
        $optionsSelect = clone $baseSelect;

        $write->query(
            ' UPDATE `'.$optionStatReplaceTable.'` '.
            ' SET uses = uses / 2 ');

        $optionsSelect
            ->from(array('h' => $optionHitReplaceTable))
            ->columns(array('option_id' => 'h.option_id', 'count' => 'COUNT(h.option_id)'))
            ->group('h.option_id');
        $statement = $write->query($optionsSelect);

        while ($row = $statement->fetch())
        {
            $uses = $row['count'] / 2;
            $updateStatement = $write->query(
            	' UPDATE `'.$optionStatReplaceTable.'` '.
            	' SET uses = uses + '.$write->quote($uses).' '.
            	' WHERE option_id = '.(int)$row['option_id'].' ');

            if (!$updateStatement->rowCount())
            {
                $write->insert($optionStatReplaceTable, array(
                	'option_id'    => $row['option_id'], 
                	'attribute_id' => Mage::getModel('eav/entity_attribute_option')->load($row['option_id'])->getAttributeId(), 
                	'uses'         => $uses, 
                    ));
            }
        }

        $write->query(
        	' RENAME TABLE `'.$this->getTable('eav_attribute_option_stat').'` TO `'.$tmpTable.'` '.
        	' , `'.$optionStatReplaceTable.'` TO `'.$this->getTable('eav_attribute_option_stat').'` ');
        $write->query('DROP TABLE IF EXISTS `'.$tmpTable.'`');

        $this->_collecAttributesStats();
    }

    protected function _collecAttributesStats()
    {
        // Collect attributes' stats
        /* @var $attributes Mage_Eav_Model_Mysql4_Entity_Attribute_Collection */
        $productEntityTypeId = Mage::getModel('eav/entity_type')->loadByCode('catalog_product')->getId();

        $attributes = Mage::getModel('eav/entity_attribute')->getCollection();
        $attributes
            ->addFieldToFilter('entity_type_id', $productEntityTypeId)
            ->getSelect()
            ->join(array('cea' => $this->getTable('catalog/eav_attribute')), 'cea.attribute_id = main_table.attribute_id')
            ->where('(cea.is_filterable = 1 OR cea.is_filterable_in_search = 1)');

        /* @var $attribute Mage_Eav_Model_Entity_Attribute */
        foreach ($attributes as $attribute)
        {
            $options = Mage::getModel('eav/entity_attribute_option')->getCollection()
                ->addFieldToFilter('attribute_id', $attribute->getId());

            if ($options->getSize())
            {
                $attrOptions = array();
                foreach ($options as $option)
                {
                    $attrOptions[] = $option->getId();
                }

                $optionsSelect = Mage::getModel('adjnav/eav_entity_attribute_option_stat')->getCollection()
                    ->addFieldToFilter('option_id', array('in' => $attrOptions))
                    ->getSelect()
                    ->columns(array())
                    ->columns(array('uses_sum' => 'SUM(uses)'));
                $attributeUses = $this->getReadConnection()->query($optionsSelect)->fetch();

                $this->_getWriteAdapter()->insertOnDuplicate($this->getTable('eav_attribute_stat'), array(
                	'attribute_id' => $attribute->getId(), 
                	'uses'         => $attributeUses['uses_sum'] / count($attrOptions), 
                    ));
            }
        }
    }
} } 