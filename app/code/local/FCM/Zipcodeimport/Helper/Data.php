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
class FCM_Zipcodeimport_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function fcmImportData($headers, $row, $carriersArr) {
		$merged = array();		
		foreach($headers as $k=>$header) {
			if(trim($header) == 'carrier_name'){
				$merged['blinkecarrier_id'] = $carriersArr[strtolower(trim($row[$k]))];
			}else{			
				$merged[trim($header)] = $row[$k];
			}
		}		
		return $merged;
	}
	
	public function fcmImportCarriersData($headers, $row) {
		$merged = array();		
		foreach($headers as $k=>$header) {
			$merged[trim($header)] = $row[$k];
		}		
		return $merged;
	}
}
