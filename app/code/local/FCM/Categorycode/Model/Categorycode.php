<?php

class FCM_Categorycode_Model_Categorycode extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('categorycode/categorycode');
    }
}