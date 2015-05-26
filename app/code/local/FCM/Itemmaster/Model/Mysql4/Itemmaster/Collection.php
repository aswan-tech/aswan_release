<?php

class FCM_Itemmaster_Model_Mysql4_Itemmaster_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('itemmaster/itemmaster');
    }
}