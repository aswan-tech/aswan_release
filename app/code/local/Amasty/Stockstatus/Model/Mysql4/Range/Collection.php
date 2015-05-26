<?php
/**
* @copyright Amasty.
*/
class Amasty_Stockstatus_Model_Mysql4_Range_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('amstockstatus/range');
    }
}