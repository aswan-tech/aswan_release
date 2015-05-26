<?php

class FCM_Productsale_Model_Mysql4_Productsale_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('productsale/productsale');
    }
}