<?php

class FCM_Seo_Model_Seo extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('seo/seo');
    }
}