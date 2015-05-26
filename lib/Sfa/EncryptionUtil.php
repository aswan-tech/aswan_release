
<?php
include('phpseclib/Crypt/DES.php');
include('phpseclib/Crypt/Hash.php');
##
#
# EncryptionUtil class is used for getHMAC and getKey functions. These method is for getting the digest value for a particular string using key.
# getKey function key value is base 64 encoded. actual key value is "1236541239871234".
# If you want to change the key first you need to genearate base 64 encode value of actual key value. Then you need to use that key.
#

 class EncryptionUtil
{

  #default constructor of EncryptionUtil class
	function EncryptionUtil()
	{
		#TODO: Add constructor logic here
	}

	function hexstr($hexstr) {
		$hexstr = str_replace(' ', '', $hexstr);
		$retstr = pack('H*', $hexstr);
		return $retstr;
	}
	function strhex($string) {
  		$hexstr = unpack('H*', $string);
  		return array_shift($hexstr);
	}
	function getHMAC($astrResponseData, $astrFileName, $astrMerchantID) {

		$strkey = $this->getKey($astrMerchantID, $astrFileName);
		$strhexkey = $this->hexstr($strkey);

		$hash = new Crypt_Hash('sha1');
		$hash->setKey($strhexkey);

		$digest = $hash->hash($astrResponseData);
		$cleardigest = $this->strhex($digest);
		return $cleardigest;

	}
	function getKey($astrMerchantID, $astrFileName) {


		$fh = fopen($astrFileName, 'r');
		$strmodulas = fgets($fh);
		$strmodulas = trim($strmodulas);
		fclose($fh);

		$lMKey = base64_encode($astrMerchantID.$astrMerchantID);

		$des = new Crypt_DES(CRYPT_DES_MODE_ECB);

		$des->setKey($this->hexstr(base64_decode($lMKey)));

		$cleartext = $des->decrypt($this->hexstr($strmodulas));

		$hexkey = $this->strhex($cleartext);
		$hexkey = strlen($hexkey)<=40?$hexkey:substr($hexkey,0,40);
		return $hexkey;
	}

}

?>