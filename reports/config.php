<?php 
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 'On');
#include_once('classes/dbconnection.class.php');
#include_once('classes/login.class.php');
#include_once('classes/reviews.class.php');

require '/home/cloudpanel/htdocs/www.americanswan.com/current/app/Mage.php';
Mage::app('admin')->setUseSessionInUrl(false);

$core_read = Mage::getSingleton('core/resource')->getConnection('core_read');
$core_write = Mage::getSingleton('core/resource')->getConnection('core_write');

$__username = 'americanswan';
$__password = 'americanswan';
$_media_path = '/tmp/reports/';
$_media_url = 'http://www.americanswan.com/media';
?>
