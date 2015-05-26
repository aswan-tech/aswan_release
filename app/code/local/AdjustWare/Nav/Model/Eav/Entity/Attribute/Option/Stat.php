<?php
/**
 * Product:     Layered Navigation Pro for Enterprise Edition - 16/08/12
 * Package:     AdjustWare_Nav_10.4.9_10.0.0_557110
 * Purchase ID: hXgQU3oI8FOfJ8PDCk5s6h6XPM5oH2Kx0N6TCAPkqN
 * Generated:   2013-04-22 06:59:44
 * File path:   app/code/local/AdjustWare/Nav/Model/Eav/Entity/Attribute/Option/Stat.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Nav')){ kUrqBiwjDyIZBmrk('6417e8c8e1b2e1d8ae29e178cbf60619'); ?><?php

/**
 * @author ksenevich
 */
class AdjustWare_Nav_Model_Eav_Entity_Attribute_Option_Stat extends Mage_Core_Model_Abstract
{
    protected static $_sortedOptions;

    protected function _construct()
    {
        $this->_init('adjnav/eav_entity_attribute_option_stat');
    }

    public function addStat($optionIds)
    {
        $this->getResource()->addStat($optionIds);
        
        return $this;
    }

    public function recalculateStat()
    {
        $this->getResource()->recalculateStat();

        return $this;
    }

    public function getSortedOptions($attributeId)
    {
        $this->prepareSortedOptions();

        if (isset(self::$_sortedOptions[$attributeId]))
        {
            return self::$_sortedOptions[$attributeId];
        }

        return array();
    }

    public function prepareSortedOptions()
    {
        if (is_null(self::$_sortedOptions))
        {
            $featuredLimit = Mage::helper('adjnav/featured')->getFeaturedValuesLimit();
            $featuredLimitDisabled = 0 == $featuredLimit;
            $collection    = $this->getCollection()->addOrder('uses', 'DESC');

            foreach ($collection->getItems() as $stat)
            {
                if (!isset(self::$_sortedOptions[$stat->getAttributeId()]))
                {
                    self::$_sortedOptions[$stat->getAttributeId()] = array();
                }

				$count = count(self::$_sortedOptions[$stat->getAttributeId()]) < $featuredLimit;
                $end = (float)end(self::$_sortedOptions[$stat->getAttributeId()]) == $stat->getUses();

                if ($count || $end || $featuredLimitDisabled)
                {
                    self::$_sortedOptions[$stat->getAttributeId()][$stat->getOptionId()] = (float)$stat->getUses();
                }
            }
        }

        return $this;
    }
} } 