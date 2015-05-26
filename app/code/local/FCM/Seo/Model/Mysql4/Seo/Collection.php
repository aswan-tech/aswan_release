<?php

class FCM_Seo_Model_Mysql4_Seo_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('seo/seo');
    }
}