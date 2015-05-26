<?php
/**
* @copyright Amasty.
*/
class Amasty_Stockstatus_Model_Mysql4_Range extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('amstockstatus/range', 'entity_id');
    }
    
    public function truncate()
    {
        $this->_getWriteAdapter()->truncate($this->getMainTable());
    }
    
    public function loadByQty(Mage_Core_Model_Abstract $object, $qty)
    {
        $read = $this->_getReadAdapter();
        
        if ($read && !is_null($qty)) {
            
            $select = $this->_getReadAdapter()->select()
                           ->from($this->getMainTable())
                           ->where($this->getMainTable().'.'.'qty_from'.'<= ?', $qty)
                           ->where($this->getMainTable().'.'.'qty_to'.'>= ?', $qty);
                           
            $data = $read->fetchRow($select);

            if ($data) {
                $object->setData($data);
            }
        }
    }

}