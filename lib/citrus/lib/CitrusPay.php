<?php

// Tested on PHP 5.2, 5.3

require_once 'Zend/Config/Ini.php';

if (!function_exists('curl_init')) {
  throw new Exception('CURL PHP extension is required.');
}
if (!function_exists('json_decode')) {
  throw new Exception('JSON PHP extension is required.');
}


abstract class CitrusPay
{
  public static $apiKey;
  public static $apiBase;
  public static $verifySslCerts = true;
  public static $env;
  public static $CPBase; 
  const VERSION = '1.6.5';

  public static function getApiKey()
  {
    return self::$apiKey;
  }

  public static function setApiKey($apiKey,$env)
  {
    self::$apiKey = $apiKey;
    self::$env = $env;
    if($env == 'sandbox' || $env == 'staging' || $env == 'production')
    	$config = new Zend_Config_Ini('citrus/lib/config/citruspay.ini', $env);
    else
    	$config = new Zend_Config_Ini('citrus/lib/config/citruspay.ini', 'sandbox');
    
    self::setApiBase($config->apiUrl);
    self::setCPBase($config->citrupay);
  }
  
  public static function getApiBase()
  {
  	return self::$apiBase;
  }
  
  public static function setApiBase($apiBase)
  {
  	self::$apiBase = $apiBase;
  }
  
  public static function getCPBase()
  {
  	return self::$CPBase;
  }
  
  public static function setCPBase($CPBase)
  {
  	self::$CPBase = $CPBase;
  }
  
  public static function getVerifySslCerts() {
    return self::$verifySslCerts;
  }

  public static function setVerifySslCerts($verify) {
    self::$verifySslCerts = $verify;
  }
}

set_include_path('../lib'.PATH_SEPARATOR.get_include_path());
//set_include_path('../lib/log4php'.PATH_SEPARATOR.get_include_path());
include('log4php/Logger.php');
Logger::configure(dirname(__FILE__).'/config/config.xml');
$log = Logger::getLogger('myLogger');
//Library
require(dirname(__FILE__) . '/Zend/Crypt.php');

// Utilities
require(dirname(__FILE__) . '/CitrusPay/Util/CitrusPaySet.php');

// Errors
require(dirname(__FILE__) . '/CitrusPay/CitrusPayError.php');
require(dirname(__FILE__) . '/CitrusPay/CitrusPayApiError.php');
require(dirname(__FILE__) . '/CitrusPay/CitrusPayApiConnectionError.php');
require(dirname(__FILE__) . '/CitrusPay/CitrusPayInvalidRequestError.php');

// Plumbing
require(dirname(__FILE__) . '/CitrusPay/CitrusPayObject.php');
require(dirname(__FILE__) . '/CitrusPay/CitrusPayApiRequestor.php');
require(dirname(__FILE__) . '/CitrusPay/CitrusPayApiResource.php');
require(dirname(__FILE__) . '/CitrusPay/CitrusPayRequestData.php');

// CitrusPay API Resources
require(dirname(__FILE__) . '/CitrusPay/Transaction.php');
require(dirname(__FILE__) . '/CitrusPay/Enquiry.php');
require(dirname(__FILE__) . '/CitrusPay/Refund.php');
require(dirname(__FILE__) . '/CitrusPay/EnquiryCollection.php');
require(dirname(__FILE__) . '/CitrusPay/Transactionsearch.php');
require(dirname(__FILE__) . '/CitrusPay/TransactionsearchCollection.php');
