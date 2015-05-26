<?php

class FCM_Categorycode_Model_Mysql4_Categorycode extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the categorycode_id refers to the key field in your database table.
        $this->_init('categorycode/categorycode', 'categorycode_id');
    }
}