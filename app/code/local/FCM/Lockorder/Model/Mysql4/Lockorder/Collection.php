<?php

class FCM_Lockorder_Model_Mysql4_Lockorder_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('lockorder/lockorder');
    }
}