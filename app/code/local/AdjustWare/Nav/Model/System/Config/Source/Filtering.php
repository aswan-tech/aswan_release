<?php
/**
 * Product:     Layered Navigation Pro for Enterprise Edition - 16/08/12
 * Package:     AdjustWare_Nav_10.4.9_10.0.0_557110
 * Purchase ID: hXgQU3oI8FOfJ8PDCk5s6h6XPM5oH2Kx0N6TCAPkqN
 * Generated:   2013-04-22 06:59:44
 * File path:   app/code/local/AdjustWare/Nav/Model/System/Config/Source/Filtering.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Nav')){ acZoMhqBerCjMkZa('989ed529993e3716f63f28f0e8b1923b'); ?><?php
class AdjustWare_Nav_Model_System_Config_Source_Filtering extends Varien_Object
{
    public function toOptionArray()
    {
        $options = array();   
        
        
        $options[] = array(
                'value'=> 'OR',
                'label' => Mage::helper('adjnav')->__('OR'),
        );

	$options[] = array(
                'value'=> 'AND',
                'label' => Mage::helper('adjnav')->__('AND'),
        );

        return $options;
    }
} } 