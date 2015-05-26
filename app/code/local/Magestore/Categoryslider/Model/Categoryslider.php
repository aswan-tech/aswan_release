<?php

class Magestore_Categoryslider_Model_Categoryslider extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('categoryslider/categoryslider');
    }
}