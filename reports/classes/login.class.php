<?php 
class AdminLogin
{
	public function login( $user_name, $user_pass )
	{
		$query = "SELECT * FROM admin WHERE user_name ='".$user_name."'AND user_pass = '".md5($user_pass)."'";
		$getResult = mysql_query($query) or die('error'.mysql_error());
		$getRows = mysql_num_rows( $getResult );
		if($getRows > 0 ){
			$getRecord = mysql_fetch_assoc($getResult);
			$_SESSION['id'] = $getRecord['id'];
			$_SESSION['user_name'] = $getRecord['user_name'];
			$_SESSION['user_pass'] = $getRecord['user_pass'];
		}
		return $getRows;
	}
	public function changePass( $user_pass )
	{
		$sql = "UPDATE admin SET user_pass = '".md5($user_pass)."'";
		$getResult = mysql_query($sql) or die('Error'. mysql_error());
		return $getResult;
	}
}

class Registration 
{
	public function UserRegister( $user_name, $user_pass, $user_email, $country, $city , $reg_status)
	{
		$sql = "SELECT count( * ) AS Total FROM `users` WHERE user_email = '".$user_email."' LIMIT 0 , 30";
		$getRes = mysql_query($sql) or die('Error'. mysql_error());
		$getdata = mysql_fetch_assoc($getRes);
		if(isset($getdata['Total']) && $getdata['Total'] > 0){
			return false;
		}
		else{
		$query = "INSERT INTO  users ( user_name, user_pass, user_email, country, city, reg_status
		) VALUES ('".$user_name."', '".$user_pass."', '".$user_email."', '".$country."', '".$city."', '".$reg_status."')";
		$getResult = mysql_query($query) or die('Error'. mysql_error());
		return $getResult;
		}
	}
	public function UserLogin( $user_pass, $user_email)
	{
		$sql = "SELECT * FROM users WHERE user_pass ='".$user_pass."' AND user_email = '".$user_email."'";
		$getUserResult = mysql_query($sql) or die('error'.mysql_error());
		$getUserRows = mysql_num_rows( $getUserResult );
		if($getUserRows > 0 ){
			$getRecord = mysql_fetch_assoc($getUserResult);
			$_SESSION['user_id'] = $getRecord['user_id'];
			$_SESSION['user_email'] = $getRecord['user_email'];
			$_SESSION['user_name'] = $getRecord['user_name'];
			return $getUserRows;
		}		
	}	
	public function userLogout()
	{
		unset($_SESSION['user_id']);
		unset($_SESSION['user_pass']);
		unset($_SESSION['user_email']);
		//$_SESSION['createLogoutMgs'] = LOGOUT_MSG;
		header('location:forum.php');
		exit;
	}
	public function addPhoto($user_img , $user_id)
	{
		$query = "UPDATE users SET user_img = '".$user_img."' WHERE user_id = $user_id ";
		$getResult = mysql_query($query) or die('Error'. mysql_error());
		move_uploaded_file($_FILES['file']['tmp_name'],'images/'.$_FILES['file']['name']);
		return $getResult;
	}
	public function getUserPhotoById($user_id)
	{	
		$sql = "SELECT user_img FROM users WHERE user_id = $user_id";
		$getResult = mysql_query($sql) or die('Error'. mysql_error());
		$getUserPhoto = mysql_fetch_assoc($getResult);
		return $getUserPhoto;
	}
	public function getUserNameById($user_id)
	{	
		$sql = "SELECT user_name FROM users WHERE user_id = $user_id";
		$getResult = mysql_query($sql) or die('Error'. mysql_error());
		$getUserPhoto = mysql_fetch_assoc($getResult);
		return $getUserPhoto;
	}
}
function logout()
{
	unset($_SESSION['id']);
	unset($_SESSION['user_name']);
	unset($_SESSION['user_pass']);
	header('location:index.php');
	exit;
}
?>