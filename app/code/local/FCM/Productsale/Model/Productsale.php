<?php

class FCM_Productsale_Model_Productsale extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('productsale/productsale');
    }
}