<?php
/***********************************************************
 * Inventory master modules	Model
 * 
 *
 * @category    FCM
 * @package     FCM_Inventory
 * @author		Ajesh Prakash(ajesh.prakash@hcl.com) 
 * @company	HCL Technologies
 * @created Monday, June 6, 2012
 * @copyright	Four cross media
 **********************************************************/
class FCM_Inventory_IndexController extends Mage_Core_Controller_Front_Action
{    
	/**
     * Upload inventory/price/image master csv files from FTP location to database by manual click/cron URL
     **/
    public function importAction(){		
	    $cronName = $this->getRequest()->getParam('cronname');
        Mage::getModel('inventory/inventory')->importitemCsv($cronName); 		
    }
}