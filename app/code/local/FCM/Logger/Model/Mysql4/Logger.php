<?php

class FCM_Logger_Model_Mysql4_Logger extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the logger_id refers to the key field in your database table.
        $this->_init('logger/logger', 'logger_id');
    }
	
}