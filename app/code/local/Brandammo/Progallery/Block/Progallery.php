<?php
class Brandammo_Progallery_Block_Progallery extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getProgallery()     
     { 
        if (!$this->hasData('progallery')) {
            $this->setData('progallery', Mage::registry('progallery'));
        }
        return $this->getData('progallery');
        
    }
}