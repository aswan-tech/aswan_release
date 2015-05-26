<?php

class FCM_Lockorder_Model_Mysql4_Lockorder extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the lockorder_id refers to the key field in your database table.
        $this->_init('lockorder/lockorder', 'lockorder_id');
    }
}