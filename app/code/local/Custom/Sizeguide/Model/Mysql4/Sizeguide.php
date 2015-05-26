<?php

class Custom_Sizeguide_Model_Mysql4_Sizeguide extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the sizeguide_id refers to the key field in your database table.
        $this->_init('sizeguide/sizeguide', 'sizeguide_id');
    }
}