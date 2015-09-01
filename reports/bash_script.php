<?php 
#require_once '/home/cloudpanel/htdocs/www.americanswan.com/current/app/Mage.php';
require_once '/var/www/aswan_release/app/Mage.php';
ini_set('display_errors', 1);
Mage::app();

$filesArr = scandir($filename_path, 1);

$item_master_file_path = "";
$filename_path = "/mnt/lecomotf/inbound/itemmaster/";



$recordCount = 0;
$totalRecordCount = 0;

$adapter = Mage::getModel('catalog/convert_adapter_productimport');
$filesRead = fopen($filename_path, 'r');
$headersData = fgetcsv($filesRead);
$errors = array();
$errorsSku = array();
$consolidatedMesage = "";
while ($data = fgetcsv($filesRead)) {
	$recordCount++;
	$mergedData = Mage::helper('itemmaster')->fcmImportData($headersData, $data);
	try {
		$adapter->saveRow($mergedData);
		$totalRecordCount++;
	} catch (Exception $e) {
		echo $mergedData['sku']."error in sku \n";
		echo $e->getMessage()."\n";
		$errorsSku[] = $mergedData['sku'];
		$errors[] = $e->getMessage();
		continue;
	}
}
if (count($errors) < 1) {
	echo $successMessage = "Item master products imported successfully." . $totalRecordCount . " out of Total " . $recordCount . " record imported successfully -> " . $filename;
}else{
	foreach ($errorsSku as $skuKey => $skuVal) {
		if (strlen($skuVal) > 0) {
			$skuMsgs .= $skuVal . ", ";
		}
	}
	if (strlen($skuMsgs) > 0) {
		$errorMessage .= 'SKUs not imported are ' . $skuMsgs."\n";
	}
	echo $errorMessage;
}
?>
