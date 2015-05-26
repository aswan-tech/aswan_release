<?php

class Brandammo_Progallery_Model_Progallery extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('progallery/progallery');
    }
}