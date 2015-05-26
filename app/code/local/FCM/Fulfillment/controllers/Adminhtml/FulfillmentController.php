<?php
/**
 * Magento Controller for the Adhoc run functions 
 *
 * This defines functions for the adhoc run activity.
 *
 * @category    FCM
 * @package     FCM_Fulfillment
 * @author	Pawan Prakash Gupta
 * @author_id	51405591
 * @company	HCL Technologies
 * @created Monday, June 4, 2012
 * @copyright	Four cross media
 */

/**
 * Controller for the Adhoc run functions
 *
 * @category    FCM
 * @package     FCM_Fulfillment
 * @author      Pawan Prakash Gupta <51405591>
 */
 
class FCM_Fulfillment_Adminhtml_FulfillmentController extends Mage_Adminhtml_Controller_action
{
	/*
	 * Order Fulfillment cron adhoc run 
	 * 
	 */
	 
	public function exportOtfAction() 
	{	
		Mage::log('Order Fulfillment adhoc run started', Zend_Log::DEBUG, 'fulfillment');
		
		echo "Order Fulfillment... <br />";
		echo "Processing, please wait... <br />";
			
		try {		
			$processModel = Mage::getModel('fulfillment/process');
			
			$startTime = $processModel->getCurrentDateTime();
			//Set Cron status to 'Processing'
			Mage::getModel('logger/cron')->updateCron("order_fulfillment", "Processing", $startTime, "", "Adhoc run started");
			
			//Add message to logger
			Mage::getModel('logger/logger')->saveLogger("order_fulfillment", "Information", __FILE__, "Adhoc run started");
			
			//run feed generation function
			$otfModel = Mage::getModel('fulfillment/otf');
			$otfModel->otffeed();
			
			//Set Cron status to 'Finished'
			$finishTime = $processModel->getCurrentDateTime();
			$summary = $otfModel->getOrdersProcessedSummary();
			Mage::getModel('logger/cron')->updateCron("order_fulfillment", "Finished", "", $finishTime, "Adhoc run completed successfully. ". $summary);
			
			//Add message to logger
			Mage::getModel('logger/logger')->saveLogger("order_fulfillment", "Success", __FILE__, "Adhoc run completed successfully. ". $summary);
			
		} catch (Exception $e) {
			$errmsg = $e->getMessage() . "\n".$e->getTraceAsString();
			Mage::log($errmsg, Zend_Log::ERR, 'fulfillment');	
			
			echo "Exception occured... \n";
				
			//Add message to logger
			Mage::getModel('logger/logger')->saveLogger("order_fulfillment", "Exception", __FILE__, $errmsg);
			Mage::getModel('logger/logger')->saveLogger("order_fulfillment", "Failure", __FILE__, "Adhoc run failed");
			
			$processModel = Mage::getModel('fulfillment/process');
			
			//Set Cron status to 'Failed'
			$errTime = $processModel->getCurrentDateTime();
			Mage::getModel('logger/cron')->updateCron("order_fulfillment", "Failed", "", $errTime, "Adhoc run failed. ". $errmsg);
			
			//Send Notification Mail
			$processModel->notify("order_fulfillment", $errmsg);
		}
		
		echo "Finished execution. ";
	}
	
	/*
	 * Order Confirmation cron adhoc run 
	 * 
	 */
	 
	public function importOtcnfAction() 
	{
		Mage::log('Order Confirmation adhoc run started', Zend_Log::DEBUG, 'fulfillment');

		echo "Order Confirmation... <br />";
		echo "Processing, please wait... <br />";
		
		$confModel = Mage::getModel('fulfillment/confirmation');
		
		try {
			
			$processModel = Mage::getModel('fulfillment/process');
			
			$startTime = $processModel->getCurrentDateTime();
			//Set Cron status to 'Processing'
			Mage::getModel('logger/cron')->updateCron("order_confirm", "Processing", $startTime, "", "Adhoc run started");
			
			//Add message to logger
			Mage::getModel('logger/logger')->saveLogger("order_confirm", "Information", __FILE__, "Adhoc run started");
			
			//run order confirmation function
			$confModel->otfconfirm();
			
			$finishTime = $processModel->getCurrentDateTime();
					
			if (!$confModel->hasException) {
				//Set Cron status to 'Finished'
				$summary = $confModel->getShortProcessSummary();
				Mage::getModel('logger/cron')->updateCron("order_confirm", "Finished", "", $finishTime, "Adhoc run completed successfully. ". $summary);
				
				//Add message to logger
				Mage::getModel('logger/logger')->saveLogger("order_confirm", "Success", __FILE__, "Adhoc run completed successfully");
			} else {
				$summaryShort = $confModel->getShortProcessSummary() . " \n";
				//Set Cron status to 'Exception'
				Mage::getModel('logger/cron')->updateCron("order_confirm", "Failed", "", $finishTime, "Adhoc run failed. ". $summaryShort . $confModel->exceptionMessage);
				
				$summaryDesc = $confModel->getDetailProcessSummary();
				$summaryDesc = "<p>" . $summaryDesc . "</p>";
				//Add message to logger
				Mage::getModel('logger/logger')->saveLogger("order_confirm", "Failure", __FILE__, "Adhoc run failed" . $summaryDesc );
				
				//Send Notification Mail
				$processModel->notify("order_confirm",   "Adhoc run failed" .  $summaryDesc);
			}
			
		} catch (Exception $e) {
			$errmsg = $e->getMessage() . "\n".$e->getTraceAsString();
						
			echo "Exception occured... \n";
				
			$summaryDesc = $confModel->getDetailProcessSummary();
			$summaryDesc = "<p>" . $summaryDesc . "</p>";
			
			Mage::log($summaryDesc . $errmsg, Zend_Log::ERR, 'fulfillment');	
			
			//Add message to logger
			Mage::getModel('logger/logger')->saveLogger("order_confirm", "Exception", __FILE__, $summaryDesc . $errmsg);
			Mage::getModel('logger/logger')->saveLogger("order_confirm", "Failure", __FILE__, "Adhoc run failed");
			
			$processModel = Mage::getModel('fulfillment/process');
			
			//Set Cron status to 'Failed'
			$summaryShort = $confModel->getShortProcessSummary() . " \n";
			$errTime = $processModel->getCurrentDateTime();
			Mage::getModel('logger/cron')->updateCron("order_confirm", "Failed", "", $errTime, "Adhoc run failed. ". $summaryShort. $errmsg);
			
			//Send Notification Mail
			$processModel->notify("order_confirm",  $summaryDesc . $errmsg);
		}
		
		echo "Finished execution. ";
	}
	
	/*
	 * Order Shipment cron adhoc run 
	 * 
	 */
	 
	public function importOtshpAction() 
	{
		Mage::log('Order Shipment adhoc run started', Zend_Log::DEBUG, 'fulfillment');

		echo "Order Shipment... <br />";
		echo "Processing, please wait... <br />";
			
		$shipmentModel = Mage::getModel('fulfillment/shipment');
		
		try {
					
			$processModel = Mage::getModel('fulfillment/process');
			
			$startTime = $processModel->getCurrentDateTime();
			//Set Cron status to 'Processing'
			Mage::getModel('logger/cron')->updateCron("order_shipment", "Processing", $startTime, "", "Adhoc run started");
			
			//Add message to logger
			Mage::getModel('logger/logger')->saveLogger("order_shipment", "Information", __FILE__, "Adhoc run started");
			
			//run orders shipment function
			$shipmentModel->otfshipping();
			
			$finishTime = $processModel->getCurrentDateTime();
			
			if (!$shipmentModel->hasException) {
				//Set Cron status to 'Finished'
				$summary = $shipmentModel->getShortProcessSummary();
				Mage::getModel('logger/cron')->updateCron("order_shipment", "Finished", "", $finishTime, "Adhoc run completed successfully. ". $summary);
				
				//Add message to logger
				Mage::getModel('logger/logger')->saveLogger("order_shipment", "Success", __FILE__, "Adhoc run completed successfully");
			} else {
				$summaryShort = $shipmentModel->getShortProcessSummary() . " \n";
				//Set Cron status to 'Exception'
				Mage::getModel('logger/cron')->updateCron("order_shipment", "Failed", "", $finishTime, "Adhoc run failed. ". $summaryShort . $shipmentModel->exceptionMessage);
				
				$summaryDesc = $shipmentModel->getDetailProcessSummary();
				$summaryDesc = "<p>" . $summaryDesc . "</p>";
				//Add message to logger
				Mage::getModel('logger/logger')->saveLogger("order_shipment", "Failure", __FILE__, "Adhoc run failed" . $summaryDesc );
				
				//Send Notification Mail
				$processModel->notify("order_shipment",  "Adhoc run failed" . $summaryDesc );
			}
			
		} catch (Exception $e) {
			$errmsg = $e->getMessage() . "\n".$e->getTraceAsString();
			
			$summaryDesc = $shipmentModel->getDetailProcessSummary();
			$summaryDesc = "<p>" . $summaryDesc . "</p>";
			
			Mage::log($summaryDesc . $errmsg, Zend_Log::ERR, 'fulfillment');	
				
			echo "Exception occured... \n";
			
			//Add message to logger
			Mage::getModel('logger/logger')->saveLogger("order_shipment", "Exception", __FILE__, $summaryDesc . $errmsg);
			Mage::getModel('logger/logger')->saveLogger("order_shipment", "Failure", __FILE__, "Adhoc run failed");
			
			$processModel = Mage::getModel('fulfillment/process');
			
			//Set Cron status to 'Failed'
			$summaryShort = $cancelModel->getShortProcessSummary() . " \n";
			$errTime = $processModel->getCurrentDateTime();
			Mage::getModel('logger/cron')->updateCron("order_shipment", "Failed", "", $errTime, "Adhoc run failed. ". $summaryShort. $errmsg);
			
			//Send Notification Mail
			$processModel->notify("order_shipment", $summaryDesc . $errmsg);
		}
		
		echo "Finished execution. ";
	}	
}