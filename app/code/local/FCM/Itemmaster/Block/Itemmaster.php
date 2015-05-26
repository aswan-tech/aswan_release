<?php
class FCM_Itemmaster_Block_Itemmaster extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getItemmaster()     
     { 
        if (!$this->hasData('itemmaster')) {
            $this->setData('itemmaster', Mage::registry('itemmaster'));
        }
        return $this->getData('itemmaster');
        
    }
}