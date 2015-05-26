<?php

class FCM_Inventory_Model_Mysql4_Inventory extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the inventory_id refers to the key field in your database table.
        $this->_init('inventory/inventory', 'inventory_id');
    }
}