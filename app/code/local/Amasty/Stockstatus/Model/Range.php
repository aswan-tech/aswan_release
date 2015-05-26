<?php
/**
* @copyright Amasty.
*/
class Amasty_Stockstatus_Model_Range extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('amstockstatus/range');
    }
    
    public function clear()
    {
        $this->getResource()->truncate();
    }
    
    public function loadByQty($qty)
    {
        $this->_getResource()->loadByQty($this, $qty);
    }
}