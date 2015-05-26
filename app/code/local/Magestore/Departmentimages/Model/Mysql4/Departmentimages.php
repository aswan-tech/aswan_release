<?php

class Magestore_Departmentimages_Model_Mysql4_Departmentimages extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the departmentimages_id refers to the key field in your database table.
        $this->_init('departmentimages/departmentimages', 'departmentimages_id');
    }
}