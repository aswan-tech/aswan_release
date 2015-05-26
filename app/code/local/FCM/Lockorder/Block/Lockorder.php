<?php
class FCM_Lockorder_Block_Lockorder extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getLockorder()     
     { 
        if (!$this->hasData('lockorder')) {
            $this->setData('lockorder', Mage::registry('lockorder'));
        }
        return $this->getData('lockorder');
        
    }
}