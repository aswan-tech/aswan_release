<?php

class FCM_Logger_Model_Mysql4_Cron_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('logger/cron');
    }
}