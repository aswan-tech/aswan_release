<?php
/*
* File Name:logout.php
* Description: logout.php file is used to logout session.
* Created Date: 06 Feb 2014 ,time 5 PM
* Created By: Sanjay Kumar <sanjay@vcareall.com>
* Modified Date & Reason:
*/
include_once('config.php');
$_SESSION['logoutMsg'] = 'You have logout successfully!';
unset($_SESSION['report_username']);
header("location:index.php");
exit();
?>


