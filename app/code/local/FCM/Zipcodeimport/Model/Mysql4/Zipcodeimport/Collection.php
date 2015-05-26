<?php

class FCM_Zipcodeimport_Model_Mysql4_Zipcodeimport_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('zipcodeimport/zipcodeimport');
    }
}