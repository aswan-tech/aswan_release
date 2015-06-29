<?php

/**
 * Magento Model to define the OTF feed functions 
 *
 * This model defines the support functions for feed generation and processing.
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
 * Process model class
 *
 * @category    FCM
 * @package     FCM_Fulfillment
 * @author      Pawan Prakash Gupta <51405591>
 */
//$phpseclibdir =  dirname(__FILE__). DS  . '..' . DS . 'lib' . DS . 'phpseclib';
//set_include_path($appPath . PS . $phpseclibdir);

$phpseclibdir = Mage::getBaseDir('lib');
//set_include_path($appPath . PS . $phpseclibdir);
//require_once 'phpseclib'. DS . 'Net' . DS . 'SSH2.php';
require_once 'phpseclib' . DS . 'Net' . DS . 'SFTP.php';

class FCM_Fulfillment_Model_Process {

    private $ftpHost;
    private $ftpPort;
    private $ftpTimeout;
    private $ftpUsername;
    private $ftpPassword;
    public $datetimeformat = 'Y-m-d H:i:s';

    public function __construct() {
        $this->ftpHost = Mage::getStoreConfig('fulfillment/sftp/host');
        $this->ftpPort = Mage::getStoreConfig('fulfillment/sftp/port');
        $this->ftpTimeout = Mage::getStoreConfig('fulfillment/sftp/timeout');
        $this->ftpUsername = Mage::getStoreConfig('fulfillment/sftp/username');
        $this->ftpPassword = Mage::getStoreConfig('fulfillment/sftp/password');
		
		if (empty($this->ftpHost)) {
            $this->ftpHost = 'uat.admin.americanswan.com';
        }

        if (empty($this->ftpUsername)) {
            $this->ftpUsername = 'lecom-ftp';
        }
		
		if (empty($this->ftpPassword)) {
            $this->ftpPassword = 'secure123#$';
        }
		
        if (empty($this->ftpPort)) {
            $this->ftpPort = 22;
        }

        if (empty($this->ftpTimeout)) {
            $this->ftpTimeout = 90;
        }
    }

    /*
     * Function to return the list of DC statuses
     *
     * @return array
     */

    public function getDcStatuses() {
        $statuses = array('0' => 'Not Sent to DC',
            '1' => 'Sent to DC',
            '2' => 'Confirmed',
            '3' => 'Rejected',
            '4' => 'Shipped',
            '5' => 'Delivered',
            '6' => 'Not Delivered',
            '7'=>'Partial Shipped'
                //'7' => 'Returned',
                //'8' => 'Refunded'
        );

        return $statuses;
    }

    /*
     * Function to return the DC status for code
     *
     * @param int
     * @return string
     */

    public function getDcStatus($code) {
        $statuses = $this->getDcStatuses();
        return $statuses[$code];
    }

    /*
     * Function to return the id for the DC status
     *
     * @params string
     * @return int
     */

    public function getDcStatusCode($status) {
        $statuses = $this->getDcStatuses();
        $statusCodeArray = array_flip($statuses);

        return $statusCodeArray[$status];
    }

    /*
     * Function to return DC status codes
     *
     * @return array
     */

    public function getDcStatusCodes() {
        $statuses = $this->getDcStatuses();
        $statusCodeArray = array_flip($statuses);

        return $statusCodeArray;
    }

    /*
     * Function to order status codes
     *
     * @return array
     */

    public function getOrderStatusCodes() {
        $orderStatusCodes = array('Confirmed' => 'confirmed_by_warehouse',
            'Rejected' => 'canceled', //Only DC status change
            'Shipped' => 'shipped',
            'Delivered' => 'delivered',
            'Not Delivered' => 'not_delivered',
            'Returned' => 'order_unsuccessful',
            'Refunded' => 'order_unsuccessful',
            'Partial Shipped'=>'partial_shipped');


        return $orderStatusCodes;
    }

    /*
     * Generate and return the filename
     *
     * @params string, string
     * @return string
     */

    public function getFilename($module, $ext, $filenum="", $orderno="") {
        $store_id = Mage::app()->getStore()->getId();
        $storeTimestamp = Mage::app()->getLocale()->storeTimeStamp($store_id);

		if ($filenum) {
			$filenum = "_" . $filenum;
		}

	   $filename = $module . '_' . ( empty($orderno) ? date("YmdHis", $storeTimestamp) : $orderno ) . $filenum . '.' . $ext;

        return $filename;
    }

    /*
     * Generate and return the temporary filename
     *
     * @params string, string
     * @return string
     */

    public function getTmpFilename($module, $ext) {
        $store_id = Mage::app()->getStore()->getId();
        $storeTimestamp = Mage::app()->getLocale()->storeTimeStamp($store_id);

        $filename = $module . '_' . date("YmdHis", $storeTimestamp) . '_tmp.' . $ext;

        return $filename;
    }

    /*
     * Return the current date in format provided
     *
     * @params string
     * @return string
     */

    public function getCurrentDateTime($format="") {
        if (empty($format)) {
            $format = $this->datetimeformat;
        }

        $dt = Mage::getModel('core/date')->date($format);

        return $dt;
    }

    /*
     * Send Notification Emails
     *
     * @params string, string
     * @return boolean
     */

    public function notify($cronkey, $message) {
        switch ($cronkey) {
            case 'order_fulfillment':
                $to = Mage::getStoreConfig('orders/notificationof/notifyto');
                $cc = Mage::getStoreConfig('orders/notificationof/notifycc');
                $subject = Mage::getStoreConfig('orders/notificationof/notifysubject');

                break;

            case 'order_confirm':
                $to = Mage::getStoreConfig('orders/notificationcnf/notifyto');
                $cc = Mage::getStoreConfig('orders/notificationcnf/notifycc');
                $subject = Mage::getStoreConfig('orders/notificationcnf/notifysubject');
                break;

            case 'order_cancel':
                $to = Mage::getStoreConfig('orders/notificationship/notifyto');
                $cc = Mage::getStoreConfig('orders/notificationship/notifycc');
                $subject = Mage::getStoreConfig('orders/notificationship/notifysubject');
                break;

            case 'order_shipment':
                $to = Mage::getStoreConfig('orders/notificationcan/notifyto');
                $cc = Mage::getStoreConfig('orders/notificationcan/notifycc');
                $subject = Mage::getStoreConfig('orders/notificationcan/notifysubject');
                break;
            
            case 'item_master':
                $to = Mage::getStoreConfig("itemmaster/notificationemail/notification_email");
                $cc = Mage::getStoreConfig("itemmaster/notificationemail/notification_ccemail");
                $subject = Mage::getStoreConfig("itemmaster/notificationemail/notification_email_subject");
                break;
        }
       
        if (empty($message)) {
            $message = "No Message attached";
        }

        $sendMail = Mage::getModel('logger/logger')->sendNotificationMail($to, $cc, $subject, $message);

        return $sendMail;
    }

    /*
     * Read file from remote server
     *
     * @params string, string, boolean[optional], boolean[optional]
     * @return boolean | array
     */

    public function readFromRemote($remote, $local, $module="", $fileformat = "xml", $isfile=false, $tmode=false) {
        Mage::log('Transferring file(s) from ' . $remote . ' to ' . $local, Zend_Log::DEBUG, 'fulfillment');

        if ($tmode) {
            $mode = NET_SFTP_STRING;
        } else {
            $mode = NET_SFTP_LOCAL_FILE;
        }

        if (substr($local, -1) != DS) {
            $local .= DS;
        }

        try {
            #$ssh = new Net_SFTP($this->ftpHost, $this->ftpPort, $this->ftpTimeout);

            #if (!$ssh->login($this->ftpUsername, $this->ftpPassword)) {
            if(1){
                 #Mage::log('Remote Failed!! starting backup', Zend_Log::DEBUG, 'fulfillment');
				 Mage::log('copying all files without sftp', Zend_Log::DEBUG, 'fulfillment');
				 $scanned_files = array_diff(scandir($remote), array('..', '.'));
				 // check if there is any file 
				 if(count($scanned_files) > 0)
				 {
					 $status = array();
					 Mage::log(count($scanned_files).' files found', Zend_Log::DEBUG, 'fulfillment');
					 foreach($scanned_files as $key=>$filename)
					 {
						if(rename($remote.$filename,$local.$filename)){
							$status['success'][] = $filename;
							unlink($remote.$filename);
							Mage::log('Copying file ' . $filename . ' to ' . $local.$filename, Zend_Log::DEBUG, 'fulfillment');
						}
						else
						{
							$status['error'][] = $filename;
							Mage::log('Error Copying file ' . $filename . ' to ' . $local.$filename, Zend_Log::ERR, 'fulfillment');
						}
					}
					return $status;
				 }
				 else
				 {
					Mage::log('No File found', Zend_Log::DEBUG, 'fulfillment');
					return false;
				 } 
			}
			
			/*

            if ($isfile) {
                $tempFileName = rtrim($remote, "." . $fileformat) . ".tmp";
                $ssh->exec('mv ' . $remote . " " . $tempFileName);

                $status = $ssh->get($tempFileName, $local);

                //File not read
                if (!$status) {
                    return false;
                }

                $ssh->exec('rm -rf ' . $tempFileName);
            } else {
                $fileformatexp = "*." . $fileformat;
                $fileslist = $ssh->exec('find ' . $remote . ' -maxdepth 1 -type f -name "' . $fileformatexp . '" | sort $1');
				
				$files = explode("\n", $fileslist);

                $status = array();
                foreach ($files as $file) {
                    if (empty($file)) {
                        continue;
                    }

                    $localfile = $local . basename($file);

                    $tempFileName = rtrim($file, "." . $fileformat) . ".tmp";
                    $ssh->exec('mv ' . $file . " " . $tempFileName);

                    $stat = $ssh->get($tempFileName, $localfile);

                    if ($stat) {
                        $status['success'][] = $file;
                        //Delete the file read
                        $ssh->exec('rm -rf ' . $tempFileName);

                        Mage::log('Copying file ' . $file . ' to ' . $localfile, Zend_Log::DEBUG, 'fulfillment');
                    } else {
                        $status['error'][] = $file;
                        Mage::log('Error copying file ' . $file . ' to ' . $localfile, Zend_Log::ERR, 'fulfillment');
                    }
                }
            }

            $ssh->disconnect();

            return $status;
            */
             
        } catch (Exception $e) {
            $errmsg = $e->getMessage();
            Mage::log($errmsg, Zend_Log::ERR, 'fulfillment');

            Mage::getModel('logger/logger')->saveLogger($module, "Exception", __FILE__, $errmsg);
            $this->notify($module, $errmsg);

            return false;
        }
    }

    /*
     * Transfer file to remote server
     *
     * @params string Local file path, string Remote file path, boolean[optional]
     * @return boolean
     */

    public function sendToRemote($local, $remote, $module="", $tmode=false) {
        Mage::log('Writing file ' . $remote . ' from ' . $local, Zend_Log::DEBUG, 'fulfillment');

        if ($tmode) {
            $mode = NET_SFTP_STRING;
        } else {
            $mode = NET_SFTP_LOCAL_FILE;
        }

        try {
            /*$ssh = new Net_SFTP($this->ftpHost, $this->ftpPort, $this->ftpTimeout);
            if (!$ssh->login($this->ftpUsername, $this->ftpPassword)) {
                throw new Exception('Remote Login Failed');
                return false;
            }

            $transferStatus = $ssh->put($remote, $local, $mode);

            $ssh->disconnect();

            return $transferStatus;*/
            copy($local, $remote);
            return true;
        } catch (Exception $e) {
            $errmsg = $e->getMessage();
            Mage::log($errmsg, Zend_Log::ERR, 'fulfillment');

            Mage::getModel('logger/logger')->saveLogger($module, "Exception", __FILE__, $errmsg);
         //   Mage::getModel('fulfillment/process')->notify($module, $errmsg);

            return false;
        }
    }

    /*
     * Function to write data to file
     * 
     * @params string, string
     * @return boolean
     */

    public function writexml($filepath, $str) {
        Mage::log('Opening file for writing ' . $filepath, Zend_Log::DEBUG, 'fulfillment');

        try {
            $fp = fopen($filepath, 'w');
            $written = $this->fwrite_stream($fp, $str);
            fclose($fp);

            if ($written < strlen($str)) {
                Mage::log('Error in writing file' . $filepath, Zend_Log::ERR, 'fulfillment');
                unlink($filepath);
                return false;
            }

            Mage::log('Successfully created file ' . $filepath, Zend_Log::DEBUG, 'fulfillment');

            return true;
        } catch (Exception $e) {
            $errmsg = $e->getMessage();
            Mage::log($errmsg, Zend_Log::ERR, 'fulfillment');
            return false;
        }
    }

    /*
     * Supporting function to 'writexml' function for writing data to file
     * 
     * @params resource, string
     * @return int
     */

    public function fwrite_stream($fp, $string) {
        for ($written = 0; $written < strlen($string); $written += $fwrite) {
            $fwrite = fwrite($fp, substr($string, $written));
            if ($fwrite === false) {
                return $written;
            }
        }
        return $written;
    }

    /*
     * Disable libxml errors and allow user to fetch error information as needed
     *
     * @params boolean
     */

    public function enableLibXmlErrors($status) {
        libxml_use_internal_errors($status);
    }

    /*
     * Format error message
     * 
     * @params LibXMLError $error
     * @return string
     */

    public function libxmlGetError($error) {
        $return = "<br/>\n";

        switch ($error->level) {
            case LIBXML_ERR_WARNING:
                $return .= "<b>Warning $error->code</b>: ";
                break;
            case LIBXML_ERR_ERROR:
                $return .= "<b>Error $error->code</b>: ";
                break;
            case LIBXML_ERR_FATAL:
                $return .= "<b>Fatal Error $error->code</b>: ";
                break;
        }

        $return .= trim($error->message);

        if ($error->file) {
            $return .= " in <b>$error->file</b>";
        }

        $return .= " on line <b>$error->line</b>\n";

        return $return;
    }

    /*
     * Retrieve array of errors 
     * 
     * @return array
     */

    public function libxmlGetErrors() {
        $errors = libxml_get_errors();
        $erArray = array();

        foreach ($errors as $error) {
            $errorDesc = $this->libxmlGetError($error);
            Mage::log($errorDesc, Zend_Log::ERR, 'fulfillment');

            $erArray[] = str_replace("<br/>\n", "", $errorDesc);
        }

        libxml_clear_errors();

        return $erArray;
    }
}

