<?php

class Custom_Sizeguide_Model_Mysql4_Sizeguide_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('sizeguide/sizeguide');
    }
}