<?php

class CitrusPay_ApiRequestor
{
	public $apiKey;

	public $curl_error_flag = false;
	
	private $log;

	public function __construct($apiKey=null)
	{
		$this->_apiKey = $apiKey;
		Logger::configure(dirname(__FILE__).'/../config/config.xml');
		$this->_log = Logger::getLogger('CITRUSPAY');
	}

	public static function apiUrl($url='')
	{
		$apiBase = CitrusPay::$apiBase;
		return "$apiBase$url";
	}

	public static function utf8($value)
	{
		if (is_string($value))
			return utf8_encode($value);
		else
			return $value;
	}

	private static function _encodeObjects($d)
	{
		if ($d instanceof CitrusPay_ApiRequestor) {
			return $d->id;
		} else if ($d === true) {
			return 'true';
		} else if ($d === false) {
			return 'false';
		} else if (is_array($d)) {
			$res = array();
			foreach ($d as $k => $v)
				$res[$k] = self::_encodeObjects($v);
			return $res;
		} else {
			return $d;
		}
	}

	public static function encode($d)
	{
		return http_build_query($d, null, '&');
	}

	public function request($meth, $url, $params=null)
	{
		if (!$params)
			$params = array();
		foreach($params as $k => $v){
			if($k == 'cardNumber' || $k == 'cvvNumber')
				continue;
			
			$this->_log->info("Value of ".$k."=".$v);
		}
		list($rbody, $rcode, $myApiKey) = $this->_requestRaw($meth, $url, $params);
		$resp = $this->_interpretResponse($rbody, $rcode);

		return array($resp, $myApiKey);
	}

	public function handleApiError($rbody, $rcode, $resp)
	{
		if (!is_array($resp) || !isset($resp['resp_msg']))
		{
			/* throw new CitrusPay_ApiError("Invalid response object from API: $rbody (HTTP response code was $rcode)", $rcode, $rbody, $resp); */
			$response['resp_code'] = $rcode;
			$response['resp_msg'] = "Invalid response object from API: $rbody (HTTP response code was $rcode)";
			$this->_log->error("Error Code: ".$rcode);
			$this->_log->error("Error Message: ".$response['resp_msg']);
			return $response;
		}

		/* $error['message'] = $resp['error']; */
		switch ($rcode) {
			case 400:
			case 404:
				$response['resp_msg'] = isset($resp['resp_msg']) ? $resp['resp_msg'] : null;
			case 401:
				$response['resp_msg'] = isset($resp['resp_msg']) ? $resp['resp_msg'] : null;
			default:
				$response['resp_msg'] = isset($resp['resp_msg']) ? $resp['resp_msg'] : null;
		}
		$response['resp_code'] = $rcode;
		
		$this->_log->error("Error Code: ".$rcode);
		$this->_log->error("Error Message: ".$response['resp_msg']);

		return $response;
	}

	private function _requestRaw($meth, $url, $params)
	{
		$myApiKey = $this->_apiKey;
		if (!$myApiKey)
			$myApiKey = CitrusPay::$apiKey;

		$absUrl = $this->apiUrl($url);
		$params = self::_encodeObjects($params);
		$langVersion = phpversion();
		$uname = php_uname();
		$ua = array('bindings_version' => CitrusPay::VERSION,
				'lang' => 'php',
				'lang_version' => $langVersion,
				'publisher' => 'CitrusPay',
				'uname' => $uname);
		$headers = array('X-CitrusPay-Client-User-Agent: ' . json_encode($ua),
				'User-Agent: CitrusPay/v1 PhpBindings/' . CitrusPay::VERSION);
		list($rbody, $rcode) = $this->_curlRequest($meth, $absUrl, $headers, $params, $myApiKey);
		return array($rbody, $rcode, $myApiKey);
	}

	private function _interpretResponse($rbody, $rcode)
	{
		try {
			$resp = json_decode($rbody, true);
		} catch (Exception $e) {
			/* throw new CitrusPay_ApiError("Invalid response body from API: $rbody (HTTP response code was $rcode)", $rcode, $rbody); */
			$resp['resp_code'] = $rcode;
			if($rcode < 100)
			{
				$resp['resp_msg'] = $rbody;
			}
			else
			{
				$resp['resp_msg'] = "Invalid response body from API: $rbody (HTTP response code was $rcode)";
			}
			$this->_log->error("Error Code: ". $rcode);
			$this->_log->error("Error Message: ". $resp['resp_msg']);
			return $resp;
		}
		$resp_code = $resp['resp_code'];
		/*Block added to handle OPUS error code for refund START*/
		$curl_err = $this->curl_error_flag;
		if($resp_code <= 2 && !$curl_err && $rcode == 200)
		{
			$resp_code = 200;
			$this->_log->info("Successful response received");
		}
		/*Block added to handle OPUS error code for refund END*/
		if ($resp_code < 200 || $resp_code >= 300) {
			$rcode = $resp_code;
		}
		if ($rcode < 200 || $rcode >= 300) {
			$resp = $this->handleApiError($rbody, $rcode, $resp);
		}
		else
		{
			if($resp['resp_code'] <= 2 && !$curl_err && $rcode == 200)
			{
			}
			else
			{
				$resp['resp_code'] = $rcode;
				$resp['resp_msg'] = "SUCCESS";
				$this->_log->info("Successful response received");
			}
		}
		return $resp;
	}

	private function _curlRequest($meth, $absUrl, $headers, $params, $myApiKey)
	{
		$curl = curl_init();
		$meth = strtolower($meth);
		$opts = array();
		if ($meth == 'get') {
			$opts[CURLOPT_HTTPGET] = 1;
			if (count($params) > 0) {
				$encoded = self::encode($params);
				$absUrl = "$absUrl?$encoded";
			}
		} else if ($meth == 'post') {
			$opts[CURLOPT_POST] = 1;
			$opts[CURLOPT_POSTFIELDS] = self::encode($params);
		} else if ($meth == 'delete')  {
			$opts[CURLOPT_CUSTOMREQUEST] = 'DELETE';
			if (count($params) > 0) {
				$encoded = self::encode($params);
				$absUrl = "$absUrl?$encoded";
			}
		} else {
			$this->_log->error("Method can only be GET or POST. Something wrong, what is this method? ".$meth);
			throw new CitrusPay_ApiError("Unrecognized method $meth");
		}

		$absUrl = self::utf8($absUrl);
		$opts[CURLOPT_URL] = $absUrl;
		$this->_log->info("Request made to URL ".$absUrl);
		$opts[CURLOPT_RETURNTRANSFER] = true;
		$opts[CURLOPT_CONNECTTIMEOUT] = 25;
		$opts[CURLOPT_TIMEOUT] = 80;
		$opts[CURLOPT_RETURNTRANSFER] = true;
		$opts[CURLOPT_HTTPHEADER] = $headers;
		$makeSslRequest = CitrusPay::$verifySslCerts;

		$opts[CURLOPT_SSL_VERIFYPEER] = 0;
		$opts[CURLOPT_SSL_VERIFYHOST] = 0;

		curl_setopt_array($curl, $opts);
		$rbody = curl_exec($curl);

		$errno = curl_errno($curl);
		if ($errno == CURLE_SSL_CACERT ||
				$errno == CURLE_SSL_PEER_CERTIFICATE ||
				$errno == 77 // CURLE_SSL_CACERT_BADFILE (constant not defined in PHP though)
		) {
			array_push($headers, 'X-CitrusPay-Client-Info: {"ca":"using CitrusPay-supplied CA bundle"}');
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
			curl_setopt($curl, CURLOPT_CAINFO,
					dirname(__FILE__) . '/../CACerts/site.crt');
			$rbody = curl_exec($curl);
		}

		$rcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		if ($rbody === false) {
			$this->_log->error("Error Occured");
			$errno = curl_errno($curl);
			$message = curl_error($curl);
			curl_close($curl);
			list($rbody, $rcode) = $this->handleCurlError($errno, $message);
		}

		curl_close($curl);
		return array($rbody, $rcode);
	}

	public function handleCurlError($errno, $message)
	{
		$this->curl_error_flag = true;
		$this->_log->info("Error No.: ". $errno);
		$apiBase = CitrusPay::$apiBase;
		switch ($errno) {
			case CURLE_COULDNT_CONNECT:
			case CURLE_COULDNT_RESOLVE_HOST:
			case CURLE_OPERATION_TIMEOUTED:
				$msg = "Could not connect to CitrusPay ($apiBase).  Please check your internet connection and try again.  If this problem persists, let us know at support@CitrusPay.com.";
				break;
			case CURLE_SSL_CACERT:
			case CURLE_SSL_PEER_CERTIFICATE:
				$msg = "Could not verify CitrusPay's SSL certificate.  Please make sure that your network is not intercepting certificates.  (Try going to $apiBase in your browser.)  If this problem persists, let us know at support@CitrusPay.com.";
				break;
			default:
				$msg = "Unexpected error communicating with CitrusPay.  If this problem persists, let us know at support@CitrusPay.com.";
		}

		$msg .= "\n\n(Network error [errno $errno]: $message)";
		$this->_log->info("Error Message: ". $msg);
		/* throw new CitrusPay_ApiConnectionError($msg); */
		return array($msg, $errno);
	}
}
