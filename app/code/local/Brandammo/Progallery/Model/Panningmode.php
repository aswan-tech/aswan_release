<?php
class Brandammo_Progallery_Model_Panningmode
{
    static public function toOptionArray()
    {
        return array(
            'XY'    => Mage::helper('progallery')->__('X and Y Axes'),
            'X'    => Mage::helper('progallery')->__('X Axis Only'),
            'Y'    => Mage::helper('progallery')->__('Y Axis Only')
        );
    }
}