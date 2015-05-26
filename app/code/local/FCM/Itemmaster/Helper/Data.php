<?php
/***********************************************************
 * Item master modules Helper files
 *
 *
 * @category    FCM
 * @package     FCM_Itemmaster
 * @author	Ajesh Prakash 
 * @company	HCL Technologies
 * @created Monday, June 6, 2012
 * @copyright	Four cross media
 **********************************************************/

class FCM_Itemmaster_Helper_Data extends Mage_Core_Helper_Abstract
{

		
	public function checkFilesDirectory($path,$cronName)
    {
	   /****************************************/
	   /*    check directory and files         */
	   /****************************************/
	
		$baseDir = Mage::getBaseDir();
		$varDir = $baseDir.DS.$path;
		try{
			$directoryType  = opendir($varDir);
		
			// Grab all files from the desired folder
			$files = glob( $varDir.'*.*' );
			
			//array_multisort(array_map( 'filemtime', $files ),SORT_NUMERIC,SORT_ASC,$files); // To sort files by modification date
			if(!empty($files))
			{
				$filename = basename($files[0]);

				if(substr($filename,0,6) == "Itmstr" && $cronName == "item_master" ){	
					return $filename;
				}if(substr($filename,0,7) == "Invntry"  && $cronName == "product_inventory"){
					return $filename;
				}if(substr($filename,0,11) == "Priceupdate"  && $cronName == "price_update"){
					return $filename;
				}if(substr($filename,0,11) == "Imageupdate"  && $cronName == "image_update"){
					return $filename;
				}
			}
		}catch (Exception $e) {
			print $e->getMessage();
			die;
				
		}		
    }
	
	public function getFilesCount($path)
	{
		$baseDir = Mage::getBaseDir();
		$varDir = $baseDir.DS.$path;
		$files = glob( $varDir.'*.*' );	
		
		return count($files);
	}
	
	public function checkCronUrl($cronName)
    {
	   /****************************************/
	   /*    check directory and files         */
	   /****************************************/
	
			if($cronName == "item_master" ){					
				return true;
			}else if($cronName == "product_inventory"){
				return true;
			}else if($cronName == "price_update"){
				return true;
			}else if($cronName == "image_update"){
				return true;
			}else{
				return false;
			}
			
    }
	
	 
	 /*******************************************************/
	 /*	Read csv files after uploading  				*/
	 /*	@param string $merged data to be set in array   */
	 /*******************************************************/
	public function fcmImportData($headers, $row) {
		$merged = array();		
		foreach($headers as $k=>$header) {
			$merged[trim($header)] = $row[$k];
		}		
		return $merged;
	}
	
	
	
	
	/*******************************************************/
	/*	Refresh magento caching after uploading the sheet  */
	/*******************************************************/
	public function refresh_cache_1_4() {
		   
			try {
				Mage :: app() -> cleanCache();
				//Mage::getSingleton('core/session')->addSuccess(Mage::helper('itemmaster')->__('Magento cache cleaned.<br />'));       
			} 
			catch ( Exception $e ) {       
				Mage::getSingleton('core/session')->addError(Mage::helper('itemmaster')->__('WARNING: ' . $e -> getMessage() . "<br><br> "));
			} 
	}

}