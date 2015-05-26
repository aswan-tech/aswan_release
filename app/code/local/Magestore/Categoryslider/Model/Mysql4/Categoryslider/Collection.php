<?php

class Magestore_Categoryslider_Model_Mysql4_Categoryslider_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('categoryslider/categoryslider');
    }
}