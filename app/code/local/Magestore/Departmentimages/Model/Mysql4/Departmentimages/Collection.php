<?php

class Magestore_Departmentimages_Model_Mysql4_Departmentimages_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('departmentimages/departmentimages');
    }
}