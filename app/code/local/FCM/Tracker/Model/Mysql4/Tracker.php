<?php

class FCM_Tracker_Model_Mysql4_Tracker extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the tracker_id refers to the key field in your database table.
        $this->_init('tracker/tracker', 'tracker_id');
    }
}