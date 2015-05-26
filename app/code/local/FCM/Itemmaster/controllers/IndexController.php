<?php
/***********************************************************
 * Item master modules 
 *
 * Module for order feed generation. 
 *
 * @category    FCM
 * @package     FCM_Itemmaster
 * @author	Ajesh Prakash 
 * @company	HCL Technologies
 * @created Monday, June 6, 2012
 * @copyright	Four cross media
 **********************************************************/
class FCM_Itemmaster_IndexController extends Mage_Core_Controller_Front_Action
{
	/**
     * Upload item master csv files from FTP location to database by manual click/cron URL
     **/
    public function importAction(){	
	    $cronName = $this->getRequest()->getParam('cronname');
        Mage::getModel('itemmaster/itemmaster')->importitemCsv($cronName); 
    }


}