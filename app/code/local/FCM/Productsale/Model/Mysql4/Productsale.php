<?php

class FCM_Productsale_Model_Mysql4_Productsale extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the productsale_id refers to the key field in your database table.
        $this->_init('productsale/productsale', 'productsale_id');
    }
}