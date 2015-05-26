<?php

class Brandammo_Progallery_Model_Direction
{
    const DIRECTION_RIGHT	= 'right';
    const DIRECTION_LEFT	= 'left';
    const DIRECTION_UP	= 'up';
    const DIRECTION_DOWN	= 'down';

    static public function toOptionArray()
    {
        return array(
            self::DIRECTION_RIGHT    => Mage::helper('progallery')->__('Right'),
            self::DIRECTION_LEFT   => Mage::helper('progallery')->__('Left'),
            self::DIRECTION_UP   => Mage::helper('progallery')->__('Up'),
            self::DIRECTION_DOWN   => Mage::helper('progallery')->__('Down')
        );
    }
}