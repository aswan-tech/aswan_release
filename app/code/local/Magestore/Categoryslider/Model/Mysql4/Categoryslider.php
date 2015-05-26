<?php

class Magestore_Categoryslider_Model_Mysql4_Categoryslider extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the categoryslider_id refers to the key field in your database table.
        $this->_init('categoryslider/categoryslider', 'categoryslider_id');
    }
}