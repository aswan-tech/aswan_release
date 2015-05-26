<?php

require_once('Util.php');
require_once('Zend/Crypt/Hmac.php');

abstract class CitrusPay_ApiResource extends CitrusPay_Object
{

	private static function getLoggerHandler(){
		Logger::configure(dirname(__FILE__).'/../config/config.xml');
		$logger = Logger::getLogger('CITRUSPAY');
		return $logger;
	}
	
	public static function classUrl($class)
	{
		if ($postfix = strrchr($class, '\\'))
			$class = substr($postfix, 1);
		if (substr($class, 0, strlen('CitrusPay')) == 'CitrusPay')
			$class = substr($class, strlen('CitrusPay'));
		$class = str_replace('_', '', $class);
		$name = urlencode($class);
		$name = strtolower($name);
		return $name;
	}



	private static function _validateCall($method, $params=null, $apiKey=null)
	{
		if ($params && !is_array($params))
			throw new CitrusPay_Error("You must pass an array as the first argument to CitrusPay API method calls.");
		if ($apiKey && !is_string($apiKey))
			throw new CitrusPay_Error('The second argument to CitrusPay API method calls is an optional per-request apiKey, which must be a string.  (HINT: you can set a global apiKey by "CitrusPay::setApiKey(<apiKey>)")');
	}

	protected static function _genericCreate($class, $params=null, $apiKey=null)
	{
		$logger = self::getLoggerHandler();
		$logger->info("Transaction START");
		self::_validateCall('create', $params, $apiKey);
		$requestor = new CitrusPay_ApiRequestor($apiKey);
		$url = self::classUrl($class);
		$hmackey = self::_generateHmacKey($params,$apiKey);
		$params['signature'] = $hmackey;
		list($response, $apiKey) = $requestor->request('post', $url, $params);
		$cpObj = CitrusPay_Util::convertToCitrusPayObject($response, $apiKey, $class);
		$retObj = self::genericConstruct($cpObj,$class);
		$logger->info("Transaction END for Moto Request".$class);
		return $retObj;
	}

	private static function genericConstruct($cpObj=null,$class=null)
	{
		if($cpObj == null || $class == null)
		{
			return null;
		}
		$isCollection = false;
		$mapper = array();
		foreach(get_object_vars($cpObj) as $key=>$value)
		{
			if(CitrusPay_Util::isList($value))
			{
				$isCollection = true;
				foreach ($value as $i)
				{
					array_push($mapper,self::genericConstruct($i,$class));
				}
				break;
			}
		}
		if($isCollection)
		{
			$class = $class."Collection";
		}
		$retObj = new $class();
		foreach(get_object_vars($cpObj) as $key=>$value)
		{
			$setterName = "set_".$key;
			if(CitrusPay_Util::isList($value))
			{
				$value = $mapper;
			}
			if(method_exists($retObj,$setterName))
			{
				$retObj->$setterName($value);
			}
		}
		return $retObj;
	}

	private static function _generateHmacKey($params=null,$apiKey=null){
		$logger = self::getLoggerHandler();
		$signatureData = CitrusPay_RequestData::_generateSignatureData($params,$apiKey);
		$logger->info("Signature data is ".$signatureData);
		$hmackey = Zend_Crypt_Hmac::compute($apiKey, "sha1", $signatureData);
		$logger->info("Signature Key generated is ".$hmackey);
		return $hmackey;
	}
}
