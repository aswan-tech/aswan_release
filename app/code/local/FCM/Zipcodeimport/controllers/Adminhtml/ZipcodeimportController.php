<?php

/**
 * FCM Zipcodeimport Module 
 *
 * Module for importing zip code, city and state for address verification.
 * @category    FCM
 * @package     FCM_Zipcodeimport
 * @author	Vikrant Kumar Mishra
 * @author_id	51402601
 * @company	HCL Technologies
 * @created Thursday, June 07, 2012
 */
/* This Controller will be called while clicking Run button from admin section */
class FCM_Zipcodeimport_Adminhtml_ZipcodeimportController extends Mage_Adminhtml_Controller_action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu("system/zipcodeimport")
                ->_addBreadcrumb(Mage::helper("adminhtml")->__("Zip Code Manager"),
                        Mage::helper("adminhtml")->__("Zip Code Manager")
        );
        return $this;
    }

    public function indexAction() {
        $this->_initAction()->renderLayout();
    }

    public function editAction() {

        $zipcodeimportId = $this->getRequest()->getParam("id");
        $zipcodeimportModel = Mage::getModel("zipcodeimport/zipcodeimport")->load($zipcodeimportId);
        if ($zipcodeimportModel->getZipcodeimportId() || $zipcodeimportId == 0) {
            Mage::register("zipcodeimport_data", $zipcodeimportModel);

            $this->loadLayout();
            $this->_setActiveMenu("system/zipcodeimport");

            $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Zipcodeimport Manager"),
                    Mage::helper("adminhtml")->__("Zipcodeimport Manager"));
            $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Shipping Zipcodeimport"),
                    Mage::helper("adminhtml")->__("Shipping Zipcodeimport"));

            $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('zipcodeimport/adminhtml_zipcodeimport_edit'))
                    ->_addLeft($this->getLayout()->createBlock('zipcodeimport/adminhtml_zipcodeimport_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton("adminhtml/session")->addError(
                    Mage::helper("zipcodeimport")->__("Zipcode does not exist")
            );
            $this->_redirect("*/*/");
        }
    }

    public function saveAction() {
        if ($this->getRequest()->getPost()) {
            try {

//                Array
//                (
    //                [form_key] => c59AUgRyAI6A8pMi
    //                [state] => Andaman & Nicobar Islands
    //                [express] => 1
    //                [standard] => 1
    //                [appointment] => 1
    //                [overnite] => 1
    //                [cod] => 0
//                )

                $postData = $this->getRequest()->getPost();
                $zipcodeimportModel = Mage::getModel("zipcodeimport/zipcodeimport");

                $zipcodeimportModel->setZipcodeimportId($this->getRequest()->getParam("id"))
                        ->setState($postData["state"])
                        ->setCity($postData["city"])
                        ->setExpress($postData["express"])
                        ->setStandard($postData["standard"])
                        ->setAppointment($postData["appointment"])
                        ->setOvernite($postData["overnite"])
                        ->setCod($postData["cod"])
                        ->save();

                Mage::getSingleton("adminhtml/session")->addSuccess(
                        Mage::helper("adminhtml")->__("Zip Code was successfully saved")
                );
                Mage::getSingleton("adminhtml/session")->setzipcodeimportData(false);

                $this->_redirect("*/*/");
                return;
            } catch (Exception $e) {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                Mage::getSingleton("adminhtml/session")->setzipcodeimportData(
                        $this->getRequest()->getPost()
                );
                $this->_redirect("*/*/edit",
                        array("id" => $this->getRequest()->getParam("id"))
                );
                return;
            }
        }
        $this->_redirect("*/*/");
    }

   /* import Action under will be called for importing CSV into database table*/
	public function importAction()
    {
		$logger = Mage::getModel('logger/logger');	// logger model object will be created
		print "Import Process started...<br>";
		/* $filePath, will define on which folder the required CSV file will be uploaded */
		//$filePath = Mage::getBaseDir('media').DIRECTORY_SEPARATOR.'zipcode'.DIRECTORY_SEPARATOR.'default'. DIRECTORY_SEPARATOR;		
		$filePath = Mage::getStoreConfig('zipcodeimport/paths/zipcodeIn');		
		/* $archiveDir, will define on which folder the required CSV file will be moved after importing all the data */
		//$archiveDir = Mage::getBaseDir('var').DIRECTORY_SEPARATOR.'zipArchive'.DIRECTORY_SEPARATOR;
		$archiveDir = Mage::getStoreConfig('zipcodeimport/paths/zipcodeOut');
				
		if(!is_dir($filePath)) {
			mkdir($filePath, 0777, true);
		}
		if(!is_dir($archiveDir)) {
			mkdir($archiveDir, 0777, true);
		}
		/* Above code will provide 777 file permission to both folder */
	
		if(!$dh  = opendir($filePath)){
			$msg = "ERROR::could not open directory for reading!";
			$logger->saveLogger('Zip Code', 'Error', 'Zipcode.csv', $msg);
			print $msg;
			return;			
		}
			
		$file_array= array();
		while (false !== ($filename = readdir($dh))) {
		if(stristr($filename, '.csv'))
		  $file_array[] = $filename;					
		}
		/* temp variable to move old files to arcive */
			$files_to_be_archived = $file_array;
		/*temp variable ends */
		
		/* It will check whether any file is available for uploading in $filePath or not */
		if(count($file_array) == 0){
			$msg = 'ERROR::No file available for processing!';
			$logger->saveLogger('Zip Code', 'Error', 'Zipcode.csv', $msg);
			print $msg;
			return;
		}
		
		/* Extra code added to pick up the latest file uploaded to import zip codes */
		$file_array_length = count($file_array);
			if($file_array_length > 0)
				{
					$file_array = $file_array[$file_array_length - 1];
				}
		/* extra code ends */
		
		$path = $filePath.$file_array;
		$pathArchive = $archiveDir.$file_array;
		
		/* Open required file in readable mode */
		$dataStr = fopen($path, "r");
		if(!$dataStr){
				$msg = 'ERROR::Could not open up the file for reading!';
				$logger->saveLogger('Zip Code', 'Error', 'Zipcode.csv', $msg);
				print $msg;
				return;
			}
		/* creating resource model for zipcodeimport module and invoke it function called loadDataInfile */
		$tempZip = Mage::getResourceModel('zipcodeimport/zipcodeimport');	
		$tempZip->loadDataInfile($path, $files_to_be_archived, $filePath ,$archiveDir);
		$msg = 'Sucess::Your file has sucessfully been imported!';
		/* put status in logger after sucessful import */
		$logger->saveLogger('Zip Code', 'Success', 'Zipcode.csv', $msg);
		/* moving the processed file to archive folder and remove it from original one */
		fclose($dataStr);
		
		/* extra code added to move all extra files to archive directory */
		foreach($files_to_be_archived as $archive){
			chmod($filePath.$archive, 0777);
			copy($filePath.$archive, $archiveDir.$archive);
			if (!unlink($filePath.$archive))
				echo ("Error deleting $archive"); 
		}
		print $msg;
	}
	
	public function downloadAction()
    {
		$zipCodeData = Mage::getModel('zipcodeimport/zipcodeimport')->getCollection();
		$zipCodeData->getSelect()->join( array('carriers'=>'fcm_shippingcarriers'), 'main_table.blinkecarrier_id = carriers.blinkecarrier_id', array('carriers.*'));
		$zipCodeData->setOrder('zipcodeimport_id', 'ASC');
		
		try{
			if(count($zipCodeData->getData()) == 0){
				Mage::throwException($this->__('Sorry, No zipcodes found to download.'));
			}	
		
			$filename = 'downloadZipCodes_'.time().'.csv';
			header('Content-Type: text/csv; charset=utf-8');
			header('Content-Disposition: attachment; filename='.$filename);
			
			// create a file pointer connected to the output stream
			$fp = fopen('php://output', 'w');
			$tableHeader = Array ('state', 'city', 'zip_code', 'express', 'standard', 'appointment', 'overnite', 'cod', 'carrier_name');
			
			// output the column headings
			fputcsv($fp, $tableHeader);
			foreach($zipCodeData->getData() as $values){
				$arr_csv_data = array($values['state'], $values['city'], $values['zip_code'], $values['express'], $values['standard'], $values['appointment'], $values['overnite'], $values['cod'], $values['carrier_name'] );
				fputcsv($fp, $arr_csv_data);
			}			
			fclose($fp);
		}catch(Exception $e){
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			$this->_redirectReferer($defaultUrl=null);	
		}
	}
	
	/* import Action under will be called for importing CSV into database table*/
	public function importcarriersAction()
    {
		$logger = Mage::getModel('logger/logger');	// logger model object will be created
		print "Import Process started...<br>";
		/* $filePath, will define on which folder the required CSV file will be uploaded */
		//$filePath = Mage::getBaseDir('media').DIRECTORY_SEPARATOR.'shippingcarriers'.DIRECTORY_SEPARATOR.'default'. DIRECTORY_SEPARATOR;
		$filePath = Mage::getStoreConfig('zipcodeimport/paths/carriersIn');
		/* $archiveDir, will define on which folder the required CSV file will be moved after importing all the data */
		//$archiveDir = Mage::getBaseDir('var').DIRECTORY_SEPARATOR.'shippingcarriersArchive'.DIRECTORY_SEPARATOR;
		$archiveDir = Mage::getStoreConfig('zipcodeimport/paths/carriersOut');
		
		if(!is_dir($filePath)) {
			mkdir($filePath, 0777, true);
		}
		if(!is_dir($archiveDir)) {
			mkdir($archiveDir, 0777, true);
		}
		/* Above code will provide 777 file permission to both folder */
	
		if(!$dh  = opendir($filePath)){
			$msg = "ERROR::could not open directory for reading!";
			$logger->saveLogger('Shipping Carriers Master Data', 'Error', 'Shippingcarriers.csv', $msg);
			print $msg;
			return;			
		}
			
		$file_array= array();
		while (false !== ($filename = readdir($dh))) {
		if(stristr($filename, '.csv'))
		  $file_array[] = $filename;					
		}
		
		/* temp variable to move old files to arcive */
			$files_to_be_archived = $file_array;
		/*temp variable ends */
		
		/* It will check whether any file is available for uploading in $filePath or not */
		if(count($file_array) == 0){
			$msg = 'ERROR::No file available for processing!';
			$logger->saveLogger('Shipping Carriers Master Data', 'Error', 'Shippingcarriers.csv', $msg);
			print $msg;
			return;
		}
		
		/* Extra code added to pick up the latest file uploaded to import zip codes */
		$file_array_length = count($file_array);
			if($file_array_length > 0)
				{
					$file_array = $file_array[$file_array_length - 1];
				}
		/* extra code ends */
		
		$path = $filePath.$file_array;
		$pathArchive = $archiveDir.$file_array;
		
		/* Open required file in readable mode */
		$dataStr = fopen($path, "r");
		if(!$dataStr){
				$msg = 'ERROR::Could not open up the file for reading!';
				$logger->saveLogger('Shipping Carriers Master Data', 'Error', 'Shippingcarriers.csv', $msg);
				print $msg;
				return;
			}
		/* creating resource model for zipcodeimport module and invoke it function called loadDataInfile */
		$tempZip = Mage::getResourceModel('zipcodeimport/zipcodeimport');	
		$tempZip->loadCarriersDataInfile($path, $files_to_be_archived, $filePath ,$archiveDir);
		
		$msg = 'Sucess::Your file has sucessfully been imported!';
		/* put status in logger after sucessful import */
		$logger->saveLogger('Shipping Carriers Master Data', 'Success', 'Shippingcarriers.csv', $msg);
		/* moving the processed file to archive folder and remove it from original one */
		fclose($dataStr);
		
		/* extra code added to move all extra files to archive directory */
		foreach($files_to_be_archived as $archive){
			chmod($filePath.$archive, 0777);
			copy($filePath.$archive, $archiveDir.$archive);
			if (!unlink($filePath.$archive))
				echo ("Error deleting $archive <br><br/>"); 
		}
		print $msg;
	}	

}