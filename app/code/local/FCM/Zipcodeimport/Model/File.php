<?php
/**
 * FCM Zip Code Import Module 
 *
 * Module for importing zip code, city and state for address verification.
 *
 * @category    FCM
 * @package     FCM_Zipcodeimport
 * @author	Vikrant Kumar Mishra
 * @author_id	51402601
 * @company	HCL Technologies
 * @created Thursday, June 7, 2012
 */
class FCM_Zipcodeimport_Model_File extends Mage_Core_Model_Config_Data
{
   public function _afterSave()
    {
        Mage::getResourceModel('zipcodeimport/zipcodeimport')->save($this);
    }
}