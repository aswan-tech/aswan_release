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
class FCM_Zipcodeimport_Model_Zipcodeimport extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('zipcodeimport/zipcodeimport');
    }
}