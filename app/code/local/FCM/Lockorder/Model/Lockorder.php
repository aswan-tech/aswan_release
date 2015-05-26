<?php

class FCM_Lockorder_Model_Lockorder extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('lockorder/lockorder');
    }
}