<?php
include_once('config.php');
error_reporting(E_ALL);
ini_set("display_errors", 1);
$error_mgs = array();
$success = array();

if(isset($_POST['btnChangePass'])) {
	
}

include_once('includes/inner_header.php');
?>
<div id="main-content">          
<div class="login-content">
	<div class="login-form-change-pass">
		<p style="text-align:center;color:red;"><?php echo implode(",", $error_mgs); ?><?php echo (isset($_SESSION['succMsg']) ? $_SESSION['succMsg'] : ''); unset($_SESSION['succMsg']); ?></p>
		<form name="changepassform" method="POST" action="order_percolation.php" id="changepass" enctype="multipart/form-data">
			<ul>
				<li><label>Upload CSV:</label><input size="10" type="file" name="upload_csv" id="new_pass" class=""></li>
				<li><label>&nbsp;</label><input type="submit" name="btnChangePass" value="Submit" class=""></li>
			</ul>
		</form>		
	</div>
</div> 
</div>
<?php include_once('includes/inner_footer.php');?>
