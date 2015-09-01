<?php
include_once('config.php');
error_reporting(E_ALL);
ini_set("display_errors", 1);
$error_mgs = array();
$success = array();

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
				
				$increment_id = trim($data[0]);
				$email = trim($data[1]);
					
				if(isset($increment_id) && isset($email) && !empty($increment_id) && !empty($email)) {
					
					$_order = Mage::getModel('sales/order')->loadByIncrementId($increment_id);
					
					if($_order){
						$_order->setCustomerEmail($email);
						$_order->save();
						$success[] = $email;
					}
					else{
						$error_mgs[] = $increment_id;
					}                   
				}
				else{
					$error_mgs[] = $increment_id."=>".$email;
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
