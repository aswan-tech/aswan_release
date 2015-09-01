<?php 
$mageFilename = '/home/cloudpanel/htdocs/www.americanswan.com/current/app/Mage.php';
require_once $mageFilename;
ini_set('display_errors', 1);
Mage::app();
$script_start = microtime(true);
$logfile = "store_credit_export.log";
Mage::log("[" . date('Y-m-d H:i:s') . "] script started.", null, $logfile);

$array_record_to_flag = array();
$write = Mage::getSingleton('core/resource')->getConnection('core_write');
$read = Mage::getSingleton('core/resource')->getConnection('core_read');

$custArr = array('jewelblissindia@gmail.com', 'pulkitjain.indore.india@gmail.com', 'ritika.0887@gmail.com', 'jagmel.danoda@gmail.com', 'jewelblissindia@gmail.com');
/*
 * AND balance_delta > 0
AND is_exported = 0

 */ 
foreach($custArr as $email) {

$sql = "SELECT customer_entity.entity_id AS customer_id, customer_entity.email, history_id, balance_delta, additional_info, enterprise_customerbalance_history.updated_at FROM enterprise_customerbalance, enterprise_customerbalance_history, customer_entity 
WHERE enterprise_customerbalance.balance_id = enterprise_customerbalance_history.balance_id 
AND customer_entity.entity_id = enterprise_customerbalance.customer_id
AND customer_entity.is_active = 1
AND action IN (2, 1, 4, 5)
AND customer_entity.email = '$email'
ORDER BY history_id ASC
LIMIT 1";

$credit_updateds = $read->fetchAll($sql);
$doc  = new DOMDocument('1.0', 'utf-8');
$doc->formatOutput = true;

$StoreCreditsNode = $doc->createElement( "StoreCredits" );
$doc->appendChild( $StoreCreditsNode );
Mage::log("Found " . count($credit_updateds) . " record(s) to update.", null, $logfile);
$foundData = FALSE;
$creditDataCount = 0;
foreach($credit_updateds as $credit) {
	Mage::log("Exporting record for customer email " . $credit['email'] . " to xml object", null, $logfile);
	
	$customer_sql = "SELECT * FROM customer_entity_varchar WHERE entity_id = '".$credit['customer_id']."' AND attribute_id IN (4, 5, 6, 7) ";
	$customer_names = $read->fetchAll($customer_sql);
	$customerNameArr = array();
	$customerFullName = array();
	$customerNameArr = array('full_name' => '', 'first_name' => '', 'last_name' => '');
	foreach($customer_names as $names) {
		if (trim($names['value']) != "") {
			$customerFullName[] = $names['value'];
		}
		if ($names['attribute_id'] == 5) {
			$customerNameArr['first_name'] = $names['value'];
		} else if ($names['attribute_id'] == 7) {
			$customerNameArr['last_name'] = $names['value'];
		}
	}
	$customerNameArr['full_name'] = implode(" ", $customerFullName);
	
    $creditDataCount ++;
    $StoreCreditNode = $doc->createElement( "StoreCredit" );
    $StoreCreditsNode->appendChild( $StoreCreditNode );

    $additional_info = $credit['additional_info'];
    $order_number = "";
    if (strpos(strtolower($additional_info), "order #") !== FALSE) {
        $additional_info = trim(substr($additional_info, strpos(strtolower($additional_info), "order #") + 7, -1));
        if (strpos($additional_info, ",") !== FALSE) {
            $order_number = substr($additional_info, 0, strpos($additional_info, ","));
        } else if (strpos($additional_info, ".") !== FALSE) {
            $order_number = substr($additional_info, 0, strpos($additional_info, "."));
        } else {
			$order_number = $additional_info;
		}
    }
	
    $dataNode = $doc->createElement( 'RecordId' );
    $StoreCreditNode->appendChild( $dataNode );
    $value = $doc->createCDATASection($credit['history_id']);
    $dataNode->appendChild( $value );

    $dataNode = $doc->createElement( 'OrderNumber' );
    $StoreCreditNode->appendChild( $dataNode );
    $value = $doc->createCDATASection($order_number);
    $dataNode->appendChild( $value );

    $dataNode = $doc->createElement( 'Email' );
    $StoreCreditNode->appendChild( $dataNode );
    $value = $doc->createCDATASection($credit['email']);
    $dataNode->appendChild( $value );
	
/*	$dataNode = $doc->createElement( 'CustomerFullName' );
    $StoreCreditNode->appendChild( $dataNode );
    $value = $doc->createCDATASection($customerNameArr['full_name']);
    $dataNode->appendChild( $value );
*/	

    $dataNode = $doc->createElement( 'Amount' );
    $StoreCreditNode->appendChild( $dataNode );
    $value = $doc->createCDATASection($credit['balance_delta']);
    $dataNode->appendChild( $value );

    $dataNode = $doc->createElement( 'StoreCreditCreated' );
    $StoreCreditNode->appendChild( $dataNode );
    $value = $doc->createCDATASection($credit['updated_at']);
    $dataNode->appendChild( $value );

    $dataNode = $doc->createElement( 'AdditionalData' );
    $StoreCreditNode->appendChild( $dataNode );
    $value = $doc->createCDATASection($credit['additional_info']);
    $dataNode->appendChild( $value );

    $dataNode = $doc->createElement( 'CustomerFirstName' );
    $StoreCreditNode->appendChild( $dataNode );
    $value = $doc->createCDATASection($customerNameArr['first_name']);
    $dataNode->appendChild( $value );

    $dataNode = $doc->createElement( 'CustomerLastName' );
    $StoreCreditNode->appendChild( $dataNode );
    $value = $doc->createCDATASection($customerNameArr['last_name']);
    $dataNode->appendChild( $value );

    $foundData = TRUE;
	$array_record_to_flag[] = array('history_id' => $credit['history_id'], 'email' => $credit['email']);
	Mage::log("Exported record for customer email " . $credit['email'] . " to xml object", null, $logfile);
}
$storeCreditFeed = $doc->saveXML();
if ($foundData) {
    $filepath = "/home/cloudpanel/mnt/lecomotf/outbound/storeCredit/scf-" . date('YmdHis') . ".xml";
	Mage::log("Creating XML to " . $filepath, null, $logfile);
    try {
        $fp = fopen($filepath, 'w');
        $written = fwrite_stream($fp, $storeCreditFeed);
        fclose($fp);

        if ($written < strlen($storeCreditFeed)) {
			Mage::log("Error in writing file " . $filepath, null, $logfile);
            unlink($filepath);
        }
	else
	{
		Mage::log("Success, " . $creditDataCount . " records found. Feed XML: " . $filepath, null, $logfile);
		foreach($array_record_to_flag as $record) {
			Mage::log("Flaging record for customer email: ".$record['email']." and history_id #".$record['history_id'], null, $logfile);
			$sql = "UPDATE enterprise_customerbalance_history SET is_exported=1, exported_on=NOW() WHERE history_id='" . $record['history_id'] . "';";
			$write->query($sql);
		}
	}
    } catch (Exception $e) {
        $errmsg = $e->getMessage();
		Mage::log("Failed :: " . $errmsg, null, $logfile);
    }
} else {
	Mage::log("Success, No record found.", null, $logfile);
}
$time_elapsed = microtime(true) - $script_start;
Mage::log("[" . date('Y-m-d H:i:s') . "] script ended. Time taken: " . (round($time_elapsed / 60)) . " minut(s) \n\n\n\n\n", null, $logfile);

}
function fwrite_stream($fp, $string) {
    for ($written = 0; $written < strlen($string); $written += $fwrite) {
        $fwrite = fwrite($fp, substr($string, $written));
        if ($fwrite === false) {
            return $written;
        }
    }
    return $written;
}
