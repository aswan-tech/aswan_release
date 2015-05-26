<?php
class FCM_Inventory_Block_Inventory extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getInventory()     
     { 
        if (!$this->hasData('inventory')) {
            $this->setData('inventory', Mage::registry('inventory'));
        }
        return $this->getData('inventory');
        
    }
}