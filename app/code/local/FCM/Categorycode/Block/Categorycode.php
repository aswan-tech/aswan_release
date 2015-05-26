<?php
class FCM_Categorycode_Block_Categorycode extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getCategorycode()     
     { 
        if (!$this->hasData('categorycode')) {
            $this->setData('categorycode', Mage::registry('categorycode'));
        }
        return $this->getData('categorycode');
        
    }
}