<?php
/**
 * Product:     Layered Navigation Pro for Enterprise Edition - 16/08/12
 * Package:     AdjustWare_Nav_10.4.9_10.0.0_557110
 * Purchase ID: hXgQU3oI8FOfJ8PDCk5s6h6XPM5oH2Kx0N6TCAPkqN
 * Generated:   2013-04-22 06:59:44
 * File path:   app/code/local/AdjustWare/Nav/Helper/Featured.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Nav')){ yTkheoiDZmUaeMky('2f88d6ba9948274bf94d12b52c77254f'); ?><?php

/**
 * 
 * @author ksenevich
 */
class AdjustWare_Nav_Helper_Featured extends Mage_Core_Helper_Abstract
{
    public function isAutoRange()
    {
        return (Mage::getStoreConfig('design/adjnav_featured/collect_period') > 0);
    }

    public function collectPeriod()
    {
        return (int)Mage::getStoreConfig('design/adjnav_featured/collect_period');
    }

    public function getFeaturedAttrsLimit()
    {
        return (int)Mage::getStoreConfig('design/adjnav_featured/featured_attrs_limit');
    }

    public function getFeaturedValuesLimit()
    {
        return (int)Mage::getStoreConfig('design/adjnav_featured/featured_vals_limit');
    }

    public function isRangeAttributes()
    {
        return ($this->isAutoRange() && Mage::getStoreConfig('design/adjnav_featured/use_ranges_attr'));
    }

    public function isRangeValues()
    {
        return ($this->isAutoRange() && Mage::getStoreConfig('design/adjnav_featured/use_ranges_val'));
    }
} } 