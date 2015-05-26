<?php
/**
 * Magento Model to define the Customer feed export function 
 *
 * This model defines the  functions for feed customer generation and processing.
 *
 * @category    FCM
 * @package     FCM_CustomerExport
 * @author	Dhananjay Kumar
 * @author_id	51399184
 * @company	HCL Technologies
 * @created Thursday, June 6, 2012
 */

/**
 * Customer Export model class
 *
 * @category    FCM
 * @package     FCM_CustomerExport
 * @author      Dhananjay Kumar
 */


class FCM_CustomerExport_Model_Customerexport
{

	  /**
     * exportCsv
     * @Description: This is for daily export for new or modified customers
     * @param string $header header of csv file
     * @param string $csvData all data to write over csv file
	  * @param string $type either "full" or "partial"
     */
	
 public function exportCsv($type)
    {	
		set_time_limit(0);
		
		// Email body with exception details
		$emailBody = "<table width='100%'>
		                  <tr><td>Action Name:</td><td> Customer {{TYPE}} Export</td></tr>
						  <tr><td>Error Time:</td><td>{{TIME}}</td></tr>
						  <tr><td>Error Message:</td><td> {{ERROR_MSG}}</td></tr>
						  <tr><td>Error File:</td><td> {{ERROR_FILE}}</td></tr>
						  <tr><td>Error Line:</td><td> {{ERROR_LINE}}</td></tr>
					  </table>";
       
		 $emailBody = str_replace("{{TYPE}}", $type, $emailBody);
	
	try{
	     // get process model
		  $process =  Mage::getModel('fulfillment/process');
	     // get cron model to findout last finished time and to update the status
	     $cron = Mage::getModel('logger/cron');
		 $cron-> updateCron('customer_export', 'Processing',date('Y-m-d H:i:s',strtotime(now())),'','');
		
		       // get Export directory path
	            $csvExportFolder = Mage::getStoreConfig('customerexport/directories/custome_csv_directory');
				
				// get archive directory path
	            $csvArchiveFolder = Mage::getStoreConfig('customerexport/directories/custome_csv_archive_directory');
				
				// get remote directory path
	            $csvRemoteFolder = Mage::getStoreConfig('customerexport/directories/custome_csv_remote_directory');
				
				if($csvExportFolder == '' OR $csvArchiveFolder=='' OR $csvRemoteFolder=='')
				{
				  throw new Exception("Configuration values for Export, Remote and Archive directory are missing!");
				}
				
				// Email settings
				$toEmail = Mage::getStoreConfig('customerexport/email/to_email');
				$ccEmail = Mage::getStoreConfig('customerexport/email/cc_email');
				$emailSubject = Mage::getStoreConfig('customerexport/email/email_subject');
		
		     if($toEmail == '' OR $ccEmail=='' OR $emailSubject=='')
				{
				  throw new Exception("Configuration value for Email Settings is missing!");
				}
		
		
	    // for new and modified customers
		$toDate =  date('Y-m-d H:i:s',strtotime(now())); // current timestamp
	    if($type == "partial")
		{
			// get last finished date of customer cron
			 $fromDate = $cron->getFinishedDate('customer_export');
			
			 $collection = Mage::getResourceModel('customer/customer_collection')
				   ->addNameToSelect()
				   ->addAttributeToSelect('email')
				   ->addAttributeToSelect('created_at')
				   ->addAttributeToSelect('updated_at')
				   ->addFieldToFilter('updated_at',array('to' => $toDate ))
				   ->addFieldToFilter('updated_at',array('from' => $fromDate ));
		}
		
		// for all customers
		if($type == "full")
		{
			
			$collection = Mage::getResourceModel('customer/customer_collection')
				   ->addNameToSelect()
				   ->addAttributeToSelect('email')
				   ->addAttributeToSelect('created_at')
				   ->addAttributeToSelect('updated_at');
		}
        		
			    $data = $collection->getData();
			   
           if(!empty($data))
             {  			   
			   $header = array('entity_id'=>'entity_id','firstname'=>'firstname','middlename'=>'middlename','lastname'=>'lastname','email'=>'email','created_at'=>'created_at','updated_at'=>'updated_at');
			   $emailArray = array();
			   $csvData[0] = $header;
			   foreach($data as $value)
			   {
			     if(in_array($value['email'], $emailArray))
				    continue;
				 else
				    $emailArray[] = $value['email'];
                   
			     if( empty($value['entity_id']) || empty($value['email']) || empty($value['firstname']) || empty($value['lastname']) || empty($value['created_at']))
				     continue;
				   
			     foreach($header as $v)
				 {  
				      $filterData[$v] = $value[$v];
				 }
				
				 $csvData[] = $filterData;
			   }
			    // Base path
	            $basePath = Mage::getBaseDir('base') . '/';
				
				
				// replace base_path from config value
	            $csvArchiveFolder = str_replace("{{base_path}}", $basePath, $csvArchiveFolder);
				
				// replace base_path from config value
	            $csvExportFolder = str_replace("{{base_path}}", $basePath, $csvExportFolder);
				
				// replace base_path from config value
	            $csvRemoteFolder = str_replace("{{base_path}}", $basePath, $csvRemoteFolder);
				
				// aad directory separator if does not exist
				 if(substr($csvExportFolder, -1) != DIRECTORY_SEPARATOR) {
					$csvExportFolder .= DIRECTORY_SEPARATOR;
				}
				
				// aad directory separator if does not exist
				 if(substr($csvArchiveFolder, -1) != DIRECTORY_SEPARATOR) {
					$csvArchiveFolder .= DIRECTORY_SEPARATOR;
				}
				
				// CSV file name on basis of cyrrent datetime
				$csvFileName = "Cstmf_".date('YmdHis',strtotime(now())).".csv";
				
				$csvCreated = $this->createCSV($csvExportFolder,$csvFileName,$csvData);
				
				if($csvCreated)
				{
				  // call function to move file to remote location.
				  
				  if(Mage::getModel('fulfillment/process')->sendToRemote(trim($csvExportFolder.$csvFileName), trim($csvRemoteFolder.$csvFileName)))
				  {
					  // Then call function to move file into archive folder
					  if($this->checkDirectory($csvArchiveFolder))
					  {
							if(!rename($csvExportFolder.$csvFileName, $csvArchiveFolder.$csvFileName))
							{
							  throw new Exception("File $csvExportFolder.$csvFileName could not be moved to archive directory");
							}
					  }
					  else
					  {
					     throw new Exception("Archive Directory could not be created");
					  }
				  }
				}
				  $cron-> updateCron('customer_export', 'Finished','',$toDate,'Cron successfully completed');
		          Mage::getSingleton('core/session')->addSuccess(Mage::helper('customerexport')->__('CSV file has been created!'));
			      
			  }
			  else
			  {
			    Mage::getSingleton('core/session')->addError(Mage::helper('customerexport')->__('No Record found, NO CSV created'));
			    $cron-> updateCron('customer_export', 'Finished','',$toDate,'Cron successfully completed and no records found');
			  }
		    }
			catch (Exception $ex) {
			       $cron-> updateCron('customer_export', 'Failed','',date('Y-m-d H:i:s',strtotime(now())),'Cron failed at '.date('Y-m-d H:i:s',strtotime(now())));
				  
				   $emailBody = str_replace("{{TIME}}", date('Y-m-d H:i:s',strtotime(now())), $emailBody);
				   $emailBody = str_replace("{{ERROR_MSG}}", $ex->getMessage(), $emailBody);
				   $emailBody = str_replace("{{ERROR_FILE}}", $ex->getFile(), $emailBody);
				   $emailBody = str_replace("{{ERROR_LINE}}", $ex->getLine(), $emailBody);
				   $logger = Mage::getModel('logger/logger');
				   $description = "Customer Export failed at ". date('YmdHis',strtotime(now()))." \n 
								   ERROR MESSAGE : ".$ex->getMessage()."\n 
								   FILE NAME : ".$ex->getFile()."\n 
								   LINE NO. : ".$ex->getLine();
				   $logger->saveLogger("customer_export", "Exception", $ex->getFile(), $description);
				   
				   $toEmail = Mage::getStoreConfig('customerexport/email/to_email');
				   $ccEmail = Mage::getStoreConfig('customerexport/email/cc_email');
				   $emailSubject = Mage::getStoreConfig('customerexport/email/email_subject');
				   
				   $logger->sendNotificationMail($toEmail,$ccEmail, '', $emailBody);
				   
				   Mage::getSingleton('core/session')->addError(Mage::helper('customerexport')->__('Error in file creation<br />'.$ex->getMessage()));
				}
			
    }

   
     /**
     * checkDirectory
     * @ This is for check the directory location
     * @param string $path is the path of directory
     * @ returns boolean 
     */
   

    function checkDirectory($path)
    {
        // Check if the directory exists
        if(!file_exists($path)) {
            // If not, create it, and any parents categories
            if(!mkdir($path, 0777, true)) {
                // Failed to create directory
                throw new Exception("Directory $path could not be created");
            }

            // Change permissions on the directory so that it can be written to
            if(!chmod($path, 0777)) {
                throw new Exception("Could not set permissions on directory $path");
            }
        } elseif(!is_dir($path)) {
		    Mage::getSingleton('core/session')->addError(Mage::helper('customerexport')->__(" Path $path is not a directory "));
            throw new Exception("Path $path is not a directory");
        }

        return true;
    }
	
	 /**
     * createCSV
     * @ This is for creating csv file at given location
     * @param string $path is the path of directory
	 * @param string $csvFileName is the name of csv file
	 * @param string $data is the customers data
     * @ returns boolean 
     */
	
	function createCSV($path,$csvFileName,$data)
	{
	     if($this->checkDirectory($path))
		 { 
		     try{
					 $path = $path.$csvFileName;
					 $mage_csv = new Varien_File_Csv(); //mage CSV			
					 //write to csv file
					 $mage_csv->saveData($path, $data); //note $data will be two dimensional array
				 }
				catch (Exception $ex) {
				     throw new Exception($ex);
				}
		 }
		 return true;
	}
	

}