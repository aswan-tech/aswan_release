<?php

/* * *********************************************************
 * Item master modules 
 * 
 *
 * @category    FCM
 * @package     FCM_Itemmaster
 * @author	Ajesh Prakash 
 * @company	HCL Technologies
 * @created Monday, June 6, 2012
 * @copyright	Four cross media
 * ******************************************************** */

class FCM_Itemmaster_Model_Itemmaster extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('itemmaster/itemmaster');
    }

    /**
      Description: Upload item master csv from FTP location to database by manual click/cron url
      Input/OutPut: NA
     */
    public function importitemCsv($cronName) {
        set_time_limit(0);
        $moduleName = ucfirst(str_replace("_", " ", $cronName));
        try {


            print "Import process started...<br />";
            //Initilize customer model
            $customerModel = Mage::getModel('customerexport/customerexport');
            //logger model
            $loggerModel = Mage::getModel('logger/logger');
            //Initilize logger model
            $cronModel = Mage::getModel('logger/cron');
            //Initilize fulfillment process model
            $processModel = Mage::getModel('fulfillment/process');

            //start time
            $startTime = $processModel->getCurrentDateTime();





            //Config path setting for Archive, Archive failure and file folder
            $inboundfolderPath = Mage::getStoreConfig("itemmaster/directories/custom_csv_directory_inbound");
            $folderPath = Mage::getStoreConfig("itemmaster/directories/custom_csv_directory");
            $archiveSuccessPath = Mage::getStoreConfig("itemmaster/directories/custom_csv_archive_directory");
            $archiveFailurePath = Mage::getStoreConfig("itemmaster/directories/custom_csv_failure_directory");

            if ($folderPath == '' || $archiveSuccessPath == '' || $archiveFailurePath == '') {
                throw new Exception("Import, Archive or Archive failure directory should not be empty");
            }


            //Config path setting for email
            $toEmail = Mage::getStoreConfig("itemmaster/notificationemail/notification_email");
            $ccEmail = Mage::getStoreConfig("itemmaster/notificationemail/notification_ccemail");
            $subject = Mage::getStoreConfig("itemmaster/notificationemail/notification_email_subject");

            if ($toEmail == '' || $ccEmail == '' || $subject == '') {
                throw new Exception("To email, Cc email, and Subject field can not be empty");
            }

            /*             * ****************************************************************** */
            /* Check files exist in inbound location, move to out bound location */
            /*             * ****************************************************************** */
            $checkCronUrl = Mage::helper('itemmaster')->checkCronUrl($cronName);
            if (!$checkCronUrl) {
                $failureMessage = 'While running Item master products import some cron url issue is coming ';

                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('inventory')->__($failureMessage));
                $loggerModel->saveLogger($moduleName, $status, $filename, $failureMessage);
                $loggerModel->sendNotificationMail($toEmail, $ccEmail, $subject, $failureMessage);

                print $failureMessage;

                $finishTime = $processModel->getCurrentDateTime();
                $cronModel->updateCron($cronName, "Finished", '', $finishTime, $failureMessage);
                $this->setData('itemmaster', Mage::registry(''));
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
                /* if(count($filesExist['success'])<1){
                  $failureMessage = 'Failed to copy from inbound location '.$inboundfolderPath;
                  Mage::getSingleton('adminhtml/session')->addError(Mage::helper('itemmaster')->__($failureMessage));
                  $loggerModel-> saveLogger($moduleName, $status, $filename, $failureMessage);
                  $loggerModel-> sendNotificationMail($toEmail,$ccEmail, $subject, $failureMessage);
                  return;
                  } */

                /*                 * *********************************************************** */
                /*              Check files permission                        */
                /*                 * *********************************************************** */
                $filename = Mage::helper('itemmaster')->checkFilesDirectory($folderPath, $cronName);

                if (($filename != "" && !file_exists($varToDir . $filename)) or !$filename) {
                    $failureMessage = "While running Item master products import, it has been found that no more file exist to be imported.";

                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('itemmaster')->__($failureMessage));
                    $loggerModel->saveLogger($moduleName, $status, $filename, $failureMessage);
                    print $failureMessage;

                    $finishTime = $processModel->getCurrentDateTime();
                    $cronModel->updateCron($cronName, "Finished", '', $finishTime, $failureMessage);
                    $loggerModel->sendNotificationMail($toEmail, $ccEmail, $subject, $failureMessage);
                    $this->setData('itemmaster', Mage::registry(''));
                } else {


                    $cronStartMessage = ucfirst(str_replace("_", " ", $cronName)) . ' cron started.';
                    $statusStart = "Information";
                    $loggerModel->saveLogger($moduleName, $statusStart, $filename, $cronStartMessage);

                    /*                     * ***************************************** */
                    /*       Mannual cron setting               */
                    /*                     * ***************************************** */

                    Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
                    $recordCount = 0;
                    $totalRecordCount = 0;
                    define('IMPORT_FILE_NAME', $varToDir . $filename);
                    $adapter = Mage::getModel('catalog/convert_adapter_productimport');

                    $filesRead = fopen(IMPORT_FILE_NAME, 'r');
                    $headersData = fgetcsv($filesRead);

                    $errors = array();
                    $errorsSku = array();
                    $consolidatedMesage = "";

                    $cronModel->updateCron($cronName, "Processing", $startTime, '', $cronStartMessage);

                    while ($data = fgetcsv($filesRead)) {
                        $recordCount++;
                        $mergedData = Mage::helper('itemmaster')->fcmImportData($headersData, $data);
                        try {
                            $adapter->saveRow($mergedData);
                            $totalRecordCount++;
                        } catch (Exception $e) {
                            $errorsSku[] = $mergedData['sku'];
                            $errors[] = $e->getMessage();
                            continue;
                        }
                    }
                    if (count($errors) < 1) {

                        $successMessage = "Item master products imported successfully.<br>" . $totalRecordCount . " out of Total " . $recordCount . " record imported successfully -> " . $filename;

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
                        $errorMessage = "<b>Below ERROR found while importing Item master products (Please note: If same error is getting repeated Multiple times that means that error persist for more than 1 product SKUs because of which they are getting imported):</b> <br>";
                        $errorMessage .= ">" . implode("<br> >", $errors);
                        $errorMessage .= ' <br><br> <b>Filename -></b> ' . $filename . '<br><br><b> ALERT:</b> ' . count($errorsSku) . " out of Total " . $recordCount . " SKU's are not imported ";
                        foreach ($errorsSku as $skuKey => $skuVal) {
                            if (strlen($skuVal) > 0) {
                                $skuMsgs .= $skuVal . ", ";
                            }
                        }
                        if (strlen($skuMsgs) > 0) {
                            $errorMessage .= '<br><b>SKUs not imported are -></b> ' . $skuMsgs;
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
                    }
                    print $consolidatedMesage . "<br />";
                    Mage::helper('itemmaster')->refresh_cache_1_4();
                    //end time
                    $finishTime = $processModel->getCurrentDateTime();
                    $cronModel->updateCron($cronName, "Finished", '', $finishTime, $consolidatedMesage);
                    $loggerModel->sendNotificationMail($toEmail, $ccEmail, $subject, $consolidatedMesage);
                    $this->setData('itemmaster', Mage::registry(''));
                }
            }
        } catch (Exception $ex) {
            Mage::getSingleton('core/session')->addError(Mage::helper('itemmaster')->__($ex));
            $loggerModel->saveLogger($moduleName, "Exception", $filename, $ex);

            print $ex . "<br />";
            //end time
            $finishTime = $processModel->getCurrentDateTime();
            $cronModel->updateCron($cronName, "Failed", $startTime, $finishTime, $ex);
            $loggerModel->sendNotificationMail($toEmail, $ccEmail, $subject, $ex);
        }
    }

}