<?php
/**
 * Magento Controller to define the Customer feed export function 
 *
 * This controller defines the  functions for feed customer generation and processing.
 *
 * @category    FCM
 * @package     FCM_CustomerExport
 * @author	Dhananjay Kumar
 * @author_id	51399184
 * @company	HCL Technologies
 * @created Thursday, June 7, 2012
 */

/**
 * Customer Export model class
 *
 * @category    FCM
 * @package     FCM_CustomerExport
 * @author      Dhananjay Kumar
 */
class FCM_CustomerExport_IndexController extends Mage_Core_Controller_Front_Action {

   /*
		@Description: Method to Invoke Partial(new and updated) customer Export
		@Date: 07-June-2012
		@Author: Dhananjay Kumar
	*/
    public function partialexportAction()
    {
       Mage::getModel('customerexport/customerexport')->exportCsv('partial'); 
    }
	
		/*
		@Description: Method to Invoke Full customer Export
		@Date: 07-June-2012
		@Author: Dhananjay Kumar
	*/
	public function fullexportAction()
    {
       Mage::getModel('customerexport/customerexport')->exportCsv('full'); 
    }	
}
