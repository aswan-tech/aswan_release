<?php
include_once('config.php');
error_reporting(E_ALL);
ini_set("display_errors", 1);
$error_mgs = array();
$success = array();

			
if(isset($_POST['btnChangePass'])) {
	$filepath = $_FILES['upload_csv']['tmp_name'];
	$nameArr = explode(".",$_FILES['upload_csv']['name']);
	if($nameArr[1] != 'csv') {
		$error_mgs[] = "Invalid csv file.";
	}
	else {
		if (($handle = fopen($filepath, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {		
				if(!empty($data[0])) {
					$query = "SELECT cbh.history_id FROM `customer_entity` as c 
							inner join `enterprise_customerbalance` as cb on c.entity_id = cb.customer_id
							inner join enterprise_customerbalance_history as cbh on cb.balance_id = cbh.balance_id
							WHERE c.email = '".$data[0]."'
							AND cbh.balance_amount = '".$data[1]."'
							group by c.email";
					$historyData = $core_read->fetchRow($query);
					$historyId = $historyData['history_id'];
					if($historyId) {
						$result = createStoreCreditFile($data[0], $historyId);
						if(!empty($result)) {
							$success[] = $result;
						}
					}
				}
				else{
					$error_mgs[] = $data[0];
				}
			}
			fclose($handle);
		}
		
		if(count($success) > 0 ) {
			$_SESSION['succMsg'] = implode(",", $success)." update successfully!";
		}
	}
}

include_once('includes/inner_header.php');
?>
<div id="main-content">          
<div class="login-content">
	<div class="login-form-change-pass">
		<p style="text-align:center;color:red;"><?php echo implode(",", $error_mgs); ?><?php echo (isset($_SESSION['succMsg']) ? $_SESSION['succMsg'] : ''); unset($_SESSION['succMsg']); ?></p>
		<form name="changepassform" method="POST" action="" id="changepass" enctype="multipart/form-data">
			<ul>
				<li><label>Upload CSV:</label><input size="10" type="file" name="upload_csv" id="new_pass" class=""></li>
				<li><label>&nbsp;</label><input type="submit" name="btnChangePass" value="Submit" class=""></li>
			</ul>
		</form>		
	</div>
</div> 
</div>
<?php include_once('includes/inner_footer.php');?>
<?php
	
	function createStoreCreditFile($email, $historyId) {
		global $core_read, $core_write;
		$sql = "SELECT customer_entity.entity_id AS customer_id, customer_entity.email, history_id, balance_delta,balance_amount, additional_info, enterprise_customerbalance_history.updated_at FROM enterprise_customerbalance, enterprise_customerbalance_history, customer_entity 
					WHERE enterprise_customerbalance.balance_id = enterprise_customerbalance_history.balance_id 
					AND customer_entity.entity_id = enterprise_customerbalance.customer_id
					AND customer_entity.is_active = 1
					AND action IN (2, 1, 4, 5)
					AND customer_entity.email = '$email'
					AND history_id = '$historyId'
					LIMIT 1";

			$credit_updateds = $core_read->fetchAll($sql);
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
				$customer_names = $core_read->fetchAll($customer_sql);
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
				$value = $doc->createCDATASection($credit['balance_amount']);
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
						$core_write->query($sql);
					}
				}
				} catch (Exception $e) {
					$errmsg = $e->getMessage();
					Mage::log("Failed :: " . $errmsg, null, $logfile);
				}
				return $email;
			} 
			else {
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
?>
