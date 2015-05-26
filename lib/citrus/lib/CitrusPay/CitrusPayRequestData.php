<?php 

class CitrusPay_RequestData
{
	private static $MERCHANT_ACCESS_KEY = "merchantAccessKey";
	private static $TRANS_ID = "transactionId";
	private static $AMOUNT = "amount";
	private static $BANK_NAME = "bankName";
	private static $CONS_AND = "&";
	
	public static function _generateSignatureData($params=null,$apiKey=null){
		$reqStr = "";
		if(array_key_exists(self::$MERCHANT_ACCESS_KEY,$params))
		{
			$merchantAccessKey = $params[self::$MERCHANT_ACCESS_KEY];
			$reqStr .= self::kvPair(self::$MERCHANT_ACCESS_KEY,$merchantAccessKey);
		}
		if(array_key_exists(self::$TRANS_ID,$params))
		{
			$transID = $params[self::$TRANS_ID];
			$reqStr .= self::$CONS_AND.self::kvPair(self::$TRANS_ID,$transID);
		}
		if(array_key_exists(self::$AMOUNT,$params))
		{
			$amount = $params[self::$AMOUNT];
			$reqStr .= self::$CONS_AND.self::kvPair(self::$AMOUNT,$amount);
		}
		if(array_key_exists(self::$BANK_NAME,$params))
		{
			$bank = $params[self::$BANK_NAME];
			$reqStr .= self::$CONS_AND.self::kvPair(self::$BANK_NAME,$bank);
		}
		return $reqStr;
	}
	
	private static function kvPair($k,$v) {
		return $k."=".$v;
	}
}
?>