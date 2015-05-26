<?php
/**
* @copyright Amasty.
*/  
class Amasty_Birth_Model_Mysql4_Log extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('ambirth/log', 'log_id');
    }
}