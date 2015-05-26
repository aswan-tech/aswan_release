<?php

class FCM_Seo_Model_Mysql4_Seo extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the seo_id refers to the key field in your database table.
        $this->_init('seo/seo', 'seo_id');
    }
}