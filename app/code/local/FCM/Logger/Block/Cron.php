<?php
/**
 * FCM Logger Module 
 *
 * Module for tracking Log and Cron Detail
 *
 * @category    FCM
 * @package     FCM_Logger
 * @author	Vikrant Kumar Mishra
 * @author_id	51402601
 * @company	HCL Technologies
 * @created Thursday, June 7, 2012
 */
class FCM_Logger_Block_Cron extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getCron()     
     { 
        if (!$this->hasData('cron')) {
            $this->setData('cron', Mage::registry('cron'));
        }
        return $this->getData('cron');
        
    }
}