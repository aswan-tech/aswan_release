<?php

class FCM_Zipcodeimport_Model_Mysql4_Zipcodeimport extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        // Note that the zipcodeimport_id refers to the key field in your database table.
        $this->_init('zipcodeimport/zipcodeimport', 'zipcodeimport_id');
    }

    /* getConnected function is used to make database connection using magento adapter  */

    public function getConnected() {
        $dbArray = $this->_getWriteAdapter()->getConfig();

        $connect = mysql_connect($dbArray['host'], $dbArray['username'], $dbArray['password']);
        if (!$connect) {
            die('Could not connect: ' . mysql_error());
        }
        $db_selected = mysql_select_db($dbArray['dbname'], $connect);
        if (!$db_selected) {
            die('Database Not Connected : ' . mysql_error());
        }
        mysql_set_charset("utf8", $connect);
        mysql_query($dbArray['initStatements'], $connect);
    }

    /* it will clean up the required table and then called loadDataOperation to fresh import  */

    public function loadDataInfile($file, $files_to_be_archived, $filePath ,$archiveDir) {
        try {
            $this->loadDataOperation($file, $files_to_be_archived, $filePath ,$archiveDir);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
	
	public function loadDataOperation($file, $files_to_be_archived, $filePath ,$archiveDir) {
	
        set_time_limit(0);
		$logger = Mage::getModel('logger/logger');
		
		// fetching list of carriers from master table to replace carriers name from carriers Id in CSV data.
		$connection_read = Mage::getSingleton('core/resource')->getConnection('core_read');
		$select = $connection_read->select()->from('fcm_shippingcarriers', array('*')); 
		$carriersArray = $connection_read->fetchAll($select); // return all rows
		$carriers_custom_array = array();
		foreach($carriersArray as $k=>$v){
			$carriers_custom_array[$v['blinkecarrier_id']] = strtolower(trim($v['carrier_name']));
		}
		
		//Replace keys with corrosponsding values to pick up the carrier Id by carrier Name.
		$carriers_custom_array_flip = array_flip ( $carriers_custom_array );
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		
		//headers array to match/vaidate csv headers
		$tableHeader = Array ( 'state','zip_code','city','express','standard','appointment','overnite','cod','carrier_name') ;
		
		$filesRead = fopen($file, 'r');
        $headersData = fgetcsv($filesRead);
	
		$match = 0;
		foreach($headersData as $data)
		{
			if(in_array(strtolower($data), $tableHeader)){
					$match = 1;
			}else{
				$match = 0;				
			}
		}
		if($match==0)
		{
			echo 'ERROR::Header not match!'; 
			$this->moveToArchive($files_to_be_archived, $filePath ,$archiveDir);
			exit;
		}
		
		$connection->query("DROP TABLE IF EXISTS `temp_table`");
		
		$connection->query("CREATE TEMPORARY TABLE IF NOT EXISTS `temp_table` (
			  `zipcodeimport_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `zip_code` int(11) unsigned NOT NULL,
			  `state` varchar(255) DEFAULT '',
			  `city` varchar(255) DEFAULT '',
			  `express` int(2) NOT NULL,
			  `standard` int(2) NOT NULL,
			  `appointment` int(2) NOT NULL,
			  `overnite` int(2) NOT NULL,
			  `cod` int(2) NOT NULL,
			  `blinkecarrier_id` VARCHAR(50) NULL DEFAULT NULL,
			  PRIMARY KEY (`zipcodeimport_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
			
		$connection->query("TRUNCATE TABLE `temp_table`");
		
		$rowCount = 0;
		$filesRead = fopen($file, 'r');
        $headersData = fgetcsv($filesRead);
		while ($data = fgetcsv($filesRead)) {
			//New parameter $carriers_custom_array_flip added to replace carrier name with carrier Id.
			//CSV has the last column as carrier_name while table fcm_zipcodeimport has last column as blinkecarrier_id.
			//making required changes in fcmImportData() function.	
			$mergedData = Mage::helper('zipcodeimport')->fcmImportData($headersData, $data, $carriers_custom_array_flip);
			$connection->insert('temp_table', $mergedData);     
			$rowCount++;	
		}        
		
		// First Check: CSV file should not be blank.
		if($rowCount == 0){
			$msg = 'ERROR::No records found in CSV file!<br/><br/>';
			$logger->saveLogger('Zip Code', 'Error', $file, $msg);
			$connection->query("DROP TABLE IF EXISTS `temp_table`");			
			print $msg;			
			$this->moveToArchive($files_to_be_archived, $filePath ,$archiveDir);
			exit;
		}
		
		//Second Check: blinke_carrier name should not be blank and values should match to the values in shipping carriers master table .
		$zipcodesTemp_empty_carriers = $connection_read->fetchAll("SELECT zip_code, state, blinkecarrier_id FROM `temp_table` where blinkecarrier_id IS NULL ");
		if(count($zipcodesTemp_empty_carriers) > 0){
			$msg = 'ERROR::Missing/Undefined Carrier name for following states!<br/><br/>';
			foreach($zipcodesTemp_empty_carriers as $k1=>$v1){
				$msg .= $v1['state'] ." (".$v1['zip_code'].")<br/>";
			}			
			$logger->saveLogger('Zip Code', 'Error', $file, $msg);
			$connection->query("DROP TABLE IF EXISTS `temp_table`");			
			print $msg;			
			$this->moveToArchive($files_to_be_archived, $filePath ,$archiveDir);
			exit;
		}
		
		//Third Check: there should be no duplicate services provided by different shipping providers for same zip codes.
		$zipcodesTempArray = $connection_read->fetchAll("SELECT zip_code, state FROM `temp_table` group by `zip_code`,`state` having (sum(`express`) > 1 OR sum(`standard`) > 1 OR sum(`appointment`) > 1 OR sum(`overnite`) > 1 OR sum(`cod`) > 1)");
		
		if(count($zipcodesTempArray) > 0){
			$msg = 'ERROR::Duplicate shipping services provided by different shipping providers in following states!<br/><br/>';
			foreach($zipcodesTempArray as $k1=>$v1){
				$msg .= $v1['state'] ." (".$v1['zip_code'].")<br/>";
			}
			$logger->saveLogger('Zip Code', 'Error', $file, $msg);
			$connection->query("DROP TABLE IF EXISTS `temp_table`");
			print $msg;	
			$this->moveToArchive($files_to_be_archived, $filePath ,$archiveDir);
			exit;
		}
		
		// no issues found in the CSV data, so proceed further.
		$connection->beginTransaction();
		$connection->query("TRUNCATE TABLE {$this->getTable('zipcodeimport/zipcodeimport')}");
		$filesRead = fopen($file, 'r');
		$headersData = fgetcsv($filesRead);
		while ($data = fgetcsv($filesRead)) {
			//New parameter $carriers_custom_array_flip added to replace carrier name with carrier Id.
			//CSV has the last column as carrier_name while table fcm_zipcodeimport has last column as blinkecarrier_id.
			//making required changes in fcmImportData() function.			
			$mergedData = Mage::helper('zipcodeimport')->fcmImportData($headersData, $data, $carriers_custom_array_flip);
			$connection->insert('fcm_zipcodeimport', $mergedData);            
		}
		$connection->query("DELETE FROM core_config_data where path LIKE 'zipcodeimport/general/bannerfile'");
		$connection->commit();
				
		$connection->query("DROP TABLE IF EXISTS `temp_table`");
    }		
	
	public function moveToArchive($files_to_be_archived, $filePath ,$archiveDir){
		/* extra code added to move all extra files to archive directory */
		foreach($files_to_be_archived as $archive){
			chmod($filePath.$archive, 0777);
			copy($filePath.$archive, $archiveDir.$archive);
			if (!unlink($filePath.$archive)){
				echo ("Error deleting $archive"); 
			}
		}
	}
	
	/* it will clean up the required table and then called loadDataOperation to fresh import  */

    public function loadCarriersDataInfile($file, $files_to_be_archived, $filePath ,$archiveDir) {
        try {
            $this->loadCarriersDataOperation($file, $files_to_be_archived, $filePath ,$archiveDir);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
	
	/* it will import the given CSV into database table  */
    public function loadCarriersDataOperation($file, $files_to_be_archived, $filePath ,$archiveDir) {
	    set_time_limit(0);
		$logger = Mage::getModel('logger/logger');
		
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$tableHeader = Array ( 'blinkecarrier_id','carrier_name') ;
		$filesRead = fopen($file, 'r');
		$headersData = fgetcsv($filesRead);
		
		$match = 0;
		foreach($headersData as $data)
		{
			if(in_array(strtolower($data), $tableHeader)){
					$match = 1;
			}else{
				$match = 0;				
			}
		}
		if($match==0)
		{
			$msg = '<br/>ERROR::Header not match!<br/><br/>';
			$logger->saveLogger('Shipping Carriers Master Data', 'Error', $file, $msg);
			print $msg;		
			$this->moveToArchive($files_to_be_archived, $filePath ,$archiveDir);	
			exit;
		}
		
		$rowCount = 0;
		$filesRead = fopen($file, 'r');
		$headersData = fgetcsv($filesRead);
		while ($data = fgetcsv($filesRead)){
			$mergedData = Mage::helper('zipcodeimport')->fcmImportCarriersData($headersData, $data);
			$rowCount++;
		}
		
		if($rowCount == 0){
			$msg = '<br/>ERROR::No records found in CSV file!<br/><br/>';
			$logger->saveLogger('Shipping Carriers Master Data', 'Error', $file, $msg);
			print $msg;
			fclose($filesRead);
			$this->moveToArchive($files_to_be_archived, $filePath ,$archiveDir);	
			exit;
		}
		
		$connection->beginTransaction();
		$connection->query("TRUNCATE TABLE fcm_shippingcarriers");
		$filesRead = fopen($file, 'r');
		$headersData = fgetcsv($filesRead);
		while ($data = fgetcsv($filesRead)){
			$mergedData = Mage::helper('zipcodeimport')->fcmImportCarriersData($headersData, $data);
			$connection->insert('fcm_shippingcarriers', $mergedData);            
		}			
		//$connection->query("DELETE FROM core_config_data where path LIKE 'zipcodeimport/general/bannerfile'");
		$connection->commit();		
	}
}