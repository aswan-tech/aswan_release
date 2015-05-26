<?php

class Brandammo_Progallery_Model_Mysql4_Progallery_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('progallery/progallery');
    }
}