<?php
include_once('config.php');
$error_mgs = '';

if(isset($_POST['btnLogin'])){
	$user_name = $_POST['user_name'];
	$user_pass = $_POST['user_pass'];
	if($user_name == ''){
		$error_mgs = 'Enter User Id !';
	}
	else if($user_pass == ''){
		$error_mgs = 'Enter Password !';
	}
	else{
		if(strcmp($user_name, $__username) == '0' && strcmp($user_pass, $__password) == '0'){
			$_SESSION['report_username'] = $user_name;
			header('location:dashboard.php');
		}
		else{
			$error_mgs = 'Invalid user id and password !';
		}
	}
} 
include_once('includes/header.php');
?>
<div id="main-content">          
	<div class="login-content">
		<div class="login-form">
			<p style="text-align:center;color:red;"><?php echo $error_mgs; ?><?php echo isset($_SESSION['logoutMsg']) ? $_SESSION['logoutMsg'] : ''; unset($_SESSION['logoutMsg']); ?></p>
			<form name="mylogin" method="POST" action="" id="login">
				<ul>
					<li><label>Username:</label><input type="text" name="user_name" id="user_name" class="input_text_login"></li>
					<li><label>Password:</label><input type="password" name="user_pass" id="user_pass" class="input_text_login"></li>
					<li><label>&nbsp;</label><input type="submit" name="btnLogin" value="Login" class="login" onclick="return adminLoginq();"></li>
				</ul> 
			</form>
		</div>
	</div> 
</div>  
<?php include_once('includes/footer.php');?>
