<?php

class FCM_Itemmaster_Model_Mysql4_Itemmaster extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the itemmaster_id refers to the key field in your database table.
        $this->_init('itemmaster/itemmaster', 'itemmaster_id');
    }
}