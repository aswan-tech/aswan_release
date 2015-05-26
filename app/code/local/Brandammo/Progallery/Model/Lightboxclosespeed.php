<?php

class Brandammo_Progallery_Model_Lightboxclosespeed
{
    const SLOW	= 'slow';
    const FAST	= 'fast';

    static public function toOptionArray()
    {
        return array(
            self::SLOW    => Mage::helper('progallery')->__('Slow'),
            self::FAST   => Mage::helper('progallery')->__('Fast')
        );
    }
}