<?php

class Brandammo_Progallery_Model_Mysql4_Progallery extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the progallery_id refers to the key field in your database table.
        $this->_init('progallery/progallery', 'progallery_id');
    }
}