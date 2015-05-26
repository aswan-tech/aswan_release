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
class FCM_Tracker_Helper_Data extends Mage_Core_Helper_Abstract
{

	const REFERER_QUERY_PARAM_NAME = 'referer';
	public function getLoginPostUrl()
    {
        $params = array();
        if ($this->_getRequest()->getParam(self::REFERER_QUERY_PARAM_NAME)) {
            $params = array(
                self::REFERER_QUERY_PARAM_NAME => $this->_getRequest()->getParam(self::REFERER_QUERY_PARAM_NAME)
            );
        }
        return $this->_getUrl('tracker/index/loginPost', $params);
    }

}