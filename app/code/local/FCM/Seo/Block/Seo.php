<?php
class FCM_Seo_Block_Seo extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getSeo()     
     { 
        if (!$this->hasData('seo')) {
            $this->setData('seo', Mage::registry('seo'));
        }
        return $this->getData('seo');
        
    }
}