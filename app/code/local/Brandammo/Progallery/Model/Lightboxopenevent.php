<?php

class Brandammo_Progallery_Model_Lightboxopenevent
{
    const CLICK_EVENT	= 'click';
    const DBCLICK_EVENT	= 'dbclick';

    static public function toOptionArray()
    {
        return array(
            self::CLICK_EVENT    => Mage::helper('progallery')->__('Click'),
            self::DBCLICK_EVENT   => Mage::helper('progallery')->__('Double Click')
        );
    }
}