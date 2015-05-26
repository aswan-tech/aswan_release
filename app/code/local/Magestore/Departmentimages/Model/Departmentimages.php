<?php

class Magestore_Departmentimages_Model_Departmentimages extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('departmentimages/departmentimages');
    }
}