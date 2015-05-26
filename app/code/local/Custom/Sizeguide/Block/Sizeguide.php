<?php
class Custom_Sizeguide_Block_Sizeguide extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getSizeguide()     
     { 
        if (!$this->hasData('sizeguide')) {
            $this->setData('sizeguide', Mage::registry('sizeguide'));
        }
        return $this->getData('sizeguide');
        
    }
}