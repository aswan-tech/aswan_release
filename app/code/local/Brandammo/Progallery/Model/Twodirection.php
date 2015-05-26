<?php
class Brandammo_Progallery_Model_Twodirection
{
    const DIRECTION_LEFT	= 'left';
    const DIRECTION_UP	= 'up';

    static public function toOptionArray()
    {
        return array(
            self::DIRECTION_LEFT   => Mage::helper('progallery')->__('Left'),
            self::DIRECTION_UP   => Mage::helper('progallery')->__('Up')
        );
    }
}