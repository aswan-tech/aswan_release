<?php

/* * *********************************************************
 * Inventory master modules	Model
 * 
 *
 * @category    FCM
 * @package     FCM_Inventory
 * @author		Ajesh Prakash(ajesh.prakash@hcl.com) 
 * @company	HCL Technologies
 * @created Monday, June 6, 2012
 * @copyright	Four cross media
 * ******************************************************** */

class FCM_Inventory_Model_Inventory extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
		// setup include PATH's
		
		$in=array();
		$in[]= Mage::getBaseDir() . DIRECTORY_SEPARATOR .'magmi';
		$in[]= Mage::getBaseDir() . DIRECTORY_SEPARATOR . 'magmi'. DIRECTORY_SEPARATOR .'inc';
		$in[]= Mage::getBaseDir() . DIRECTORY_SEPARATOR .'magmi'.DIRECTORY_SEPARATOR .'integration'.DIRECTORY_SEPARATOR .'inc';
		$in[]= Mage::getBaseDir() . DIRECTORY_SEPARATOR .'magmi'. DIRECTORY_SEPARATOR .'engines';
		$inpath = get_include_path();
		foreach ($in as $i){
			$inpath .= $i .':';
		}
		$inpath .= '.';
		set_include_path($inpath);

		require_once("magmi_datapump.php");  //require for magmi import
        $this->_init('inventory/inventory');
    }

    /**
      Description: Upload inventory/price/image master csv files from FTP location to database by manual click/cron url
      Input/OutPut: NA
     */
    public function importitemCsv($cronName) {
        set_time_limit(0);
        $moduleName = ucfirst(str_replace("_", " ", $cronName));

        try {

            //Initilize customer model
            $customerModel = Mage::getModel('customerexport/customerexport');
            //Initilize logger model
            $loggerModel = Mage::getModel('logger/logger');
            //Initilize logger model
            $cronModel = Mage::getModel('logger/cron');
            //Initilize fulfillment process model
            $processModel = Mage::getModel('fulfillment/process');

            //start time
            $startTime = $processModel->getCurrentDateTime();

            //logger
            $cronStartMessage = ucfirst(str_replace("_", " ", $cronName)) . ' Cron started.';
            $statusStart = "Information";
			$filename = '';
            $loggerModel->saveLogger($moduleName, $statusStart, $filename, $cronStartMessage);


            if ($cronName == 'product_inventory') {

                //Config path setting for Archive, Archive failure and file folder
                $inboundfolderPath = Mage::getStoreConfig("inventory/directories/custom_csv_directory_inbound");
                $folderPath = Mage::getStoreConfig("inventory/directories/custom_csv_directory");
                $archiveSuccessPath = Mage::getStoreConfig("inventory/directories/custom_csv_archive_directory");
                $archiveFailurePath = Mage::getStoreConfig("inventory/directories/custom_csv_failure_directory");
                $cronText = "Products Stock";
            } else if ($cronName == 'price_update') {
                //Config path setting for Archive, Archive failure and file folder
                $inboundfolderPath = Mage::getStoreConfig("inventory/directories_priceupdate/custom_csv_directory_inbound");
                $folderPath = Mage::getStoreConfig("inventory/directories_priceupdate/custom_csv_directory");
                $archiveSuccessPath = Mage::getStoreConfig("inventory/directories_priceupdate/custom_csv_archive_directory");
                $archiveFailurePath = Mage::getStoreConfig("inventory/directories_priceupdate/custom_csv_failure_directory");
                $cronText = "Products Special price";
            }
            //check config path
            if ($inboundfolderPath == '' || $folderPath == '' || $archiveSuccessPath == '' || $archiveFailurePath == '') {
                throw new Exception("Import, Archive or Archive failure directory should not be empty");
            }

            //Config path setting for email
            $toEmail = Mage::getStoreConfig("inventory/notificationemail/notification_email");
            $ccEmail = Mage::getStoreConfig("inventory/notificationemail/notification_ccemail");
            $subject = Mage::getStoreConfig("inventory/notificationemail/notification_email_subject");

            if ($toEmail == '' || $ccEmail == '' || $subject == '') {
                throw new Exception("toemail, ccemail, and subject field can not be empty");
            }

            /*             * ****************************************************************** */
            /* Check files exist in inbound location, move to out bound location */
            /*             * ****************************************************************** */
            $checkCronUrl = Mage::helper('itemmaster')->checkCronUrl($cronName);
            if (!$checkCronUrl) {
                $failureMessage = 'While running ' . $cronText . ' feed import some cron url issue is coming';
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('inventory')->__($failureMessage));
                $loggerModel->saveLogger($moduleName, "Error", $filename, $failureMessage);
                $loggerModel->sendNotificationMail($toEmail, $ccEmail, $subject, $failureMessage);

                print $failureMessage;

                $finishTime = $processModel->getCurrentDateTime();
                $cronModel->updateCron($cronName, "Finished", '', $finishTime, $failureMessage);

                return;
            } else {

                $baseDir = Mage::getBaseDir();
                $varToDir = $baseDir . '/' . $folderPath;
                $varToArchiveSuccessDir = $baseDir . '/' . $archiveSuccessPath;
                $varToArchiveFailureDir = $baseDir . '/' . $archiveFailurePath;

                //create directory if not exist
                $customerModel->checkDirectory($varToDir);
                $customerModel->checkDirectory($varToArchiveSuccessDir);
                $customerModel->checkDirectory($varToArchiveFailureDir);

                $filesExist = $processModel->readFromRemote($inboundfolderPath, $varToDir, $cronName, $fileformat = "csv");

                /*                 * ****************************************************************** */
                /* Check files exist in inbound location, move to out bound location */
                /*                 * ****************************************************************** */
                //		  if(count($filesExist['success'])<1){
                //			$failureMessage = 'Failed to copy from inbound location '.$inboundfolderPath;
                //	        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('inventory')->__($failureMessage));
                //			$loggerModel-> saveLogger($moduleName, "Exception", $filename, $failureMessage);
                //			$loggerModel-> sendNotificationMail($toEmail,$ccEmail, $subject, $failureMessage);
                //         	return;
                //		  }
                /*                 * *********************************************************** */
                /*              Check files permission                        */
                /*                 * *********************************************************** */
                $filename = Mage::helper('itemmaster')->checkFilesDirectory($folderPath, $cronName);

                if (($filename != "" && !file_exists($varToDir . $filename)) or !$filename) {
                    $failureMessage = "While running " . $cronText . " feed import, it has been found that no more file exist to be imported";
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('inventory')->__($failureMessage));
                    $loggerModel->saveLogger($moduleName, "Error", $filename, $failureMessage);

                    if ($cronName == 'price_update') {
                        $loggerModel->sendNotificationMail($toEmail, $ccEmail, $subject, $failureMessage);
                    }

                    print $failureMessage;
                    $finishTime = $processModel->getCurrentDateTime();
                    $cronModel->updateCron($cronName, "Finished", '', $finishTime, $failureMessage);

                    return;
                } else {

                    /*                     * ***************************************** */
                    /*       Mannual cron setting               */
                    /*                     * ***************************************** */

                    Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
                    $recordCount = 0;
                    $totalRecordCount = 0;
                    define('IMPORT_FILE_NAME', $varToDir . $filename);
                    $adapter = Mage::getModel('catalog/convert_adapter_product');

                    $filesRead = fopen(IMPORT_FILE_NAME, 'r');
                    $headersData = fgetcsv($filesRead);

                    $errors = array();
                    $errorsSku = array();
                    $consolidatedMesage = "";

                    $cronModel->updateCron($cronName, "Processing", $startTime, '', $cronStartMessage);
					
					
					/* Magmi import */
					$loggerModel->sendNotificationMail($toEmail, $ccEmail, $subject, 'Inventory update start');
					
					$dp=Magmi_DataPumpFactory::getDataPumpInstance("productimport");
					$dp->beginImportSession("default","update");
					
					while ($data = fgetcsv($filesRead)) {
						$recordCount++;
						$product = Mage::helper('itemmaster')->fcmImportData($headersData, $data);
						try {
							$dp->ingest($product);
							//echo "<pre>"; print_r($product);
							$product = null;    //clear memory
							unset($product);
							$totalRecordCount++;
						} catch (Exception $e) {
                            $errorsSku[] = $mergedData['sku'];
                            $errors[] = $e->getMessage();
                            continue;
                        }
						
					}
					unset($data);
					$dp->endImportSession();
					
					$this->indexer();

                    
					/*old code
					$read = Mage::getSingleton('core/resource')->getConnection('core_read');
                    $qry = " SELECT oi.sku
							FROM `sales_flat_order` AS o
							LEFT JOIN `sales_flat_order_item` AS oi ON o.entity_id = oi.order_id
							WHERE o.`sent_to_erp` =0
							AND oi.product_type = 'simple' GROUP BY oi.sku ";
                    $res = $read->fetchAll($qry); //get array

                    foreach ($res as $skus) {
                        $ordered_sku[] = $skus['sku'];
                    }

                    while ($data = fgetcsv($filesRead)) {
                        $recordCount++;
                        $mergedData = Mage::helper('itemmaster')->fcmImportData($headersData, $data);
                        try {
                            if (in_array($mergedData['sku'], $ordered_sku)) {
                                $qry = "SELECT oi.sku, sum(oi.qty_ordered) as total
										FROM `sales_flat_order` AS o
										LEFT JOIN `sales_flat_order_item` AS oi ON o.entity_id = oi.order_id
										WHERE o.`sent_to_erp` =0
										AND oi.product_type = 'simple' and oi.sku='" . $mergedData['sku'] . "'";
                                $ordercount = $read->fetchRow($qry);
                                $mergedData['qty'] = ($mergedData['qty'] - $ordercount['total']) < 0 ? 0 : ($mergedData['qty'] - $ordercount['total']);
                            }
                            $adapter->saveRow($mergedData);
                            $totalRecordCount++;
                        } catch (Exception $e) {
                            $errorsSku[] = $mergedData['sku'];
                            $errors[] = $e->getMessage();
                            continue;
                        }
                    } */

                    if (count($errors) < 1) {
                        $successMessage = $cronText . " imported successfully.<br>" . $totalRecordCount . " out of Total " . $recordCount . " record imported successfully -> " . $filename;

                        if (rename($varToDir . $filename, $varToArchiveSuccessDir . $filename)) {
                            Mage::getSingleton('core/session')->addSuccess(Mage::helper('inventory')->__('File has been moved to archive location successfully!<br> -> ' . $archiveSuccessPath));
                        }
						
						//Count total number of files remaining to be imported						
						$filesCount = Mage::helper('itemmaster')->getFilesCount($folderPath);
						$successMessage .= "<br><br>"."Total Number of files remaining to be imported: <b>".$filesCount."</b>";
						
                        Mage::getSingleton('core/session')->addSuccess(Mage::helper('inventory')->__("<br>" . $successMessage));
                        $loggerModel->saveLogger($moduleName, "Success", $filename, $successMessage);

                        $consolidatedMesage = $successMessage;
						
                    } else {
                        $errorMessage = "<b>Below ERROR found while importing " . $cronText . " (Please note: If same error is getting repeated Multiple times that means that error persist for more than 1 " . $cronText . " SKUs because of which they are getting imported): </b><br>";
                        $errorMessage .= ">" . implode("<br> >", $errors);
                        $errorMessage .= ' <br><br> <b>Filename -></b> ' . $filename . '<br><br> <b>ALERT:</b> ' . count($errorsSku) . " out of Total " . $recordCount . " " . $cronText . " SKUs are not imported ";

                        foreach ($errorsSku as $skuKey => $skuVal) {
                            if (strlen($skuVal) > 0) {
                                $skuMsgs .= $skuVal . ", ";
                            }
                        }
                        if (strlen($skuMsgs) > 0) {
                            $errorMessage .= '<br>' . $cronText . ' <b>Stock SKUs not imported are -></b> ' . $skuMsgs;
                        }

                        if (rename($varToDir . $filename, $varToArchiveFailureDir . $filename)) {
                            Mage::getSingleton('core/session')->addError(Mage::helper('inventory')->__('File has been moved to error location!<br> -> ' . $archiveFailurePath));
                        }
						
						//Count total number of files remaining to be imported
						$filesCount = Mage::helper('itemmaster')->getFilesCount($folderPath);
						$errorMessage .= "<br><br>"."Total Number of files remaining to be imported: <b>".$filesCount."</b>";						
						
                        Mage::getSingleton('core/session')->addError(Mage::helper('inventory')->__("<br>" . $errorMessage));
                        $loggerModel->saveLogger($moduleName, "Failure", $filename, $errorMessage);
                        $consolidatedMesage = $errorMessage;
						return false;
                    }
                    Mage::helper('itemmaster')->refresh_cache_1_4();
                    //end time
                    $finishTime = $processModel->getCurrentDateTime();
                    $cronModel->updateCron($cronName, "Finished", '', $finishTime, $consolidatedMesage);
                    $loggerModel->sendNotificationMail($toEmail, $ccEmail, $subject, $consolidatedMesage);
                }
            }
        } catch (Exception $ex) {
            //$errorMessage = "Error";
            Mage::getSingleton('core/session')->addError(Mage::helper('inventory')->__($ex));
            $loggerModel->saveLogger($moduleName, "Exception", $fileName, $ex);

            //end time
            $finishTime = $processModel->getCurrentDateTime();
            $cronModel->updateCron($cronName, "Failed", $startTime, $finishTime, $ex);
            $loggerModel->sendNotificationMail($toEmail, $ccEmail, $subject, $ex);
			return false;
        }
		return true;
    }
	
	/**
	 * @method Do indexing
	 */	
	public function indexer(){
		$indexCollection = Mage::getModel('index/process')->getCollection();
		try {
			foreach ($indexCollection as $index) {
				$index->reindexAll();
			}
		} catch (Exception $e) {
			Mage::log('Indexing error = '. $e->getMessage());
			return false;
		}
		return true;
	}

}