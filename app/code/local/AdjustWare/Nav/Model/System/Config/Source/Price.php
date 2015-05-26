<?php
/**
 * Product:     Layered Navigation Pro for Enterprise Edition - 16/08/12
 * Package:     AdjustWare_Nav_10.4.9_10.0.0_557110
 * Purchase ID: hXgQU3oI8FOfJ8PDCk5s6h6XPM5oH2Kx0N6TCAPkqN
 * Generated:   2013-04-22 06:59:44
 * File path:   app/code/local/AdjustWare/Nav/Model/System/Config/Source/Price.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Nav')){ mREwjpoZaMTrjgEm('811ab5b7a4ca8794a8ada9532576bdb6'); ?><?php
class AdjustWare_Nav_Model_System_Config_Source_Price extends Varien_Object
{
    public function toOptionArray()
    {
        $options = array();
        
        $options[] = array(
                'value'=> 'default',
                'label' => Mage::helper('adjnav')->__('Default')
        );
        $options[] = array(
                'value'=> 'slider',
                'label' => Mage::helper('adjnav')->__('Slider')
        );
        $options[] = array(
                'value'=> 'input',
                'label' => Mage::helper('adjnav')->__('Input')
        );
        
        return $options;
    }
} } 