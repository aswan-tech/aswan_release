<?php
class FCM_Provider_Model_Mysql4_Provider_Collection
    extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init("provider/provider");
    }
}