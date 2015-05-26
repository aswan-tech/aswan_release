<?php
class FCM_Premiumalert_Block_Premiumalert extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getPremiumalert()     
     { 
        if (!$this->hasData('premiumalert')) {
            $this->setData('premiumalert', Mage::registry('premiumalert'));
        }
        return $this->getData('premiumalert');
        
    }
}