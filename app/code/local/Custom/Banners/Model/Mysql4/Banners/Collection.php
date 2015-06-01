<?php
 
class Custom_Banners_Model_Mysql4_Banners_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('banners/banners');
    }
}