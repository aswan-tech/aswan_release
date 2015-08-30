<?php
if(empty($_SESSION['report_username']) || !isset($_SESSION['report_username'])) {
	header("location:index.php");
	exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="charset=utf-8" />
<title> Admin Inner Page </title>
<link href="style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	ShowTime();
});
function ShowTime() {
var dt = new Date();
document.getElementById("lblTime").innerHTML = dt.toLocaleTimeString();

window.setTimeout("ShowTime()", 1000); // Here 1000(milliseconds) means one 1 Sec  
}

</script>
</head>
<body>
<div id="pagewrap">
<header id="header">
<div class="row bg-row2"><div class="logo"><center><img src="http://www.americanswan.com/skin/frontend/enterprise/lecom/images-v3/as-logo-new.png" /></center></div>
<div class="logo1">
<center>
<ul>
	<li>Welcome to <?php echo isset($_SESSION['report_username']) ? $_SESSION['report_username'] : ''; ?></li>
	<li>Time: <span id="lblTime"></span></li>
	<li><a href="logout.php">Logout</a></li>
</ul>
</center>
</div>
</div> 
<div class="row bg-row2">
<div class="col-1">
	<ul class="nav">
		<li><a href="dashboard.php">DASHBOARD</a></li>
		<!--<li><a href="managecat.php">Manage Category</a></li>
		<li><a href="product_import.php">PRODUCT IMPORT</a></li>-->
		<li><a href="update_order_email.php">UPDATE ORDER EMAIL</a></li>
		<li><a href="store_credit.php">STORE CREDIT</a></li>
		<li><a href="order_percolation.php">ORDER PERCOLATION</a></li>
	</ul>
</div> 
</div> 
</header> 
