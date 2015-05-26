<?php
/**
 * FCM Order Tracker Module 
 *
 * Module for tracking Customer Order
 *
 * @category    FCM
 * @package     FCM_Tracker
 * @author	Vikrant Kumar Mishra
 * @author_id	51402601
 * @company	HCL Technologies
 * @created Thursday, June 7, 2012
 */
class FCM_Tracker_Model_Tracker extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('tracker/tracker');
    }
}