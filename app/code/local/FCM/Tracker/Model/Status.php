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
class FCM_Tracker_Model_Status extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 2;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('tracker')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('tracker')->__('Disabled')
        );
    }
}