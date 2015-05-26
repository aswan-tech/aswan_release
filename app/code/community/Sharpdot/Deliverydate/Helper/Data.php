<?php

class Sharpdot_Deliverydate_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * Converts Date to local Date or returns a safe string
	 * 
	 * @return string: localized date or no date message
	 */
	public function getFormatedDeliveryDate($date)
	{
		//if null or 0-0-0 00:00:00 return no date string
		if(empty($date) ||$date == null || $date == '0000-00-00 00:00:00'){
			return Mage::helper('deliverydate')->__("No Delivery Date Specified.");
		}
		
		//Format Date
		$formatedDate = Mage::helper('core')->formatDate($date, 'medium', false);
		//TODO: check that date is valid before passing it back
		
		return $formatedDate; 
	}
	
	public function getFormatedDeliveryDateToSave($date = null)
	{
		if(empty($date) ||$date == null || $date == '0000-00-00 00:00:00'){
			return null;
		}
		
		$timestamp = null;
		try{
			//TODO: add Better Date Validation
			$timestamp = strtotime($date);
			$dateArray = explode("/", $date);
			if(count($dateArray) != 3){
				//invalid date
				return null;
			}
			//die($timestamp."<<");
			//$formatedDate = date('Y-m-d H:i:s', strtotime($timestamp));
			$formatedDate = date('Y-m-d H:i:s', mktime(0, 0, 0, $dateArray[0], $dateArray[1], $dateArray[2]));
		} catch(Exception $e){
			//TODO: email error 
			//return null if not converted ok
			return null;
		}				
		
		return $formatedDate; 		
	}
}