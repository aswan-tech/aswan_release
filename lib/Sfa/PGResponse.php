<?php

 ## This class is used for capturing the Response message coming form the payment gateway
 #  Data receieved from the Payment gateway is Tokenised and stored in the objects of this class
 #
   class PGResponse{

	var $mstrRespCode = null;
	var $mstrRespMessage;
	var $mstrTxnId=null;
	var $mstrEPGTxnId=null;
	var $mstrRedirectionTxnId=null;
	var $mstrRedirectionUrl=null;
	var $mstrAuthIdCode=null;
	var $mstrRRN=null;
	var $mstrTxnType=null;
	var $mstrTxnDateTime=null;
	var $mstrCVRespCode=null;
	var $mstrReserveFld1=null;
	var $mstrReserveFld2=null;
	var $mstrReserveFld3=null;
	var $mstrReserveFld4=null;
	var $mstrReserveFld5=null;
	var $mstrReserveFld6=null;
	var $mstrReserveFld7=null;
	var $mstrReserveFld8=null;
	var $mstrReserveFld9=null;
	var $mstrReserveFld10=null;


	var  $PG_RESP_RESPCODE = "RespCode";
	var  $PG_RESP_RESPMSG = "Message";
	var  $PG_RESP_EPG_TXN_ID = "ePGTxnID";
	var  $PG_RESP_MRT_TXN_ID = "TxnID";
	var  $PG_RESP_REDIRECT_TXN_ID = "RedirectionTxnID";
	var  $PG_RESP_AUTH_ID = "AuthIdCode";
	var  $PG_RESP_RRN = "RRN";
	var  $PG_RESP_TXNTYPE = "TxnType";
	var  $PG_RESP_TXN_DATE_TIME = "TxnDateTime";

	var $PG_RESP_CVRESP_CODE = "CVRespCode";
	var $PG_RESP_RESERVE1 = "Reserve1";
	var $PG_RESP_RESERVE2 = "Reserve2";
	var $PG_RESP_RESERVE3 = "Reserve3";
	var $PG_RESP_RESERVE4 = "Reserve4";
	var $PG_RESP_RESERVE5 = "Reserve5";
	var $PG_RESP_RESERVE6 = "Reserve6";
	var $PG_RESP_RESERVE7 = "Reserve7";
	var $PG_RESP_RESERVE8 = "Reserve8";
	var $PG_RESP_RESERVE9 = "Reserve9";
	var $PG_RESP_RESERVE10 = "Reserve10";


	function getCVRespCode(){
		return $this->mstrCVRespCode;
	}

	function setCVRespCode( $astrCVRespCode) {
		$this->mstrCVRespCode = $astrCVRespCode;
	}


	function getReserveFld1(){
		return $this->mstrReserveFld1;
	}


	function setReserveFld1( $astrReserveFld1) {
		$this->mstrReserveFld1 = $astrReserveFld1;
	}

	function getReserveFld2(){
		return $this->mstrReserveFld2;
	}

	function setReserveFld2( $astrReserveFld2) {
		$this->mstrReserveFld2 = $astrReserveFld2;
	}


	function getReserveFld3(){
		return $this->mstrReserveFld3;
	}

	function setReserveFld3( $astrReserveFld3) {
		$this->mstrReserveFld3 = $astrReserveFld3;
	}


	function getReserveFld4(){
		return $this->mstrReserveFld4;
	}

	function setReserveFld4( $astrReserveFld4) {
		$this->mstrReserveFld4 = $astrReserveFld4;
	}


	function getReserveFld5(){
		return $this->mstrReserveFld5;
	}

	function setReserveFld5( $astrReserveFld5) {
		$this->mstrReserveFld5 = $astrReserveFld5;
	}


	function getReserveFld6(){
		return $this->mstrReserveFld6;
	}

	function setReserveFld6( $astrReserveFld6) {
		$this->mstrReserveFld6 = $astrReserveFld6;
	}


	function getReserveFld7(){
		return $this->mstrReserveFld7;
	}

	function setReserveFld7( $astrReserveFld7) {
		$this->mstrReserveFld7 = $astrReserveFld7;
	}


	function getReserveFld8(){
		return $this->mstrReserveFld8;
	}

	function setReserveFld8( $astrReserveFld8) {
		$this->mstrReserveFld8 = $astrReserveFld8;
	}


	function getReserveFld9(){
		return $this->mstrReserveFld9;
	}

	function setReserveFld9( $astrReserveFld9) {
		$this->mstrReserveFld9 = $astrReserveFld9;
	}


	function getReserveFld10(){
		return $this->mstrReserveFld10;
	}

	function setReserveFld10( $astrReserveFld10) {
			$this->mstrReserveFld10 = $astrReserveFld10;
	}





	function getRespCode(){
		return $this->mstrRespCode;
	}

	function setRespCode( $astrRespCode) {
		$this->mstrRespCode = $astrRespCode;
	}

	function getRespMessage(){
		return $this->mstrRespMessage;
	}


	function setRespMessage($astrRespMessage) {
		$this->mstrRespMessage = $astrRespMessage;
	}

	function getTxnId(){
		return $this->mstrTxnId;
	}


	function setTxnId($astrTxnId) {
		$this->mstrTxnId = $astrTxnId;
	}

	function getEpgTxnId(){
		return $this->mstrEPGTxnId;
	}


	function setEpgTxnId( $astrEPGTxnId) {
		$this->mstrEPGTxnId = $astrEPGTxnId;
	}


	function getRedirectionTxnId() {
		return $this->mstrRedirectionTxnId;
	}

	function setRedirectionTxnId($astrRedirectionTxnId) {
		$this->mstrRedirectionTxnId = $astrRedirectionTxnId;
	}


	function getRedirectionUrl() {
		return $this->mstrRedirectionUrl;
	}


	function setRedirectionUrl($astrRedirectionUrl) {
		$this->mstrRedirectionUrl = $astrRedirectionUrl;
	}


	function getAuthIdCode() {
		return $this->mstrAuthIdCode;
	}


	function setAuthIdCode($astrAuthIdCode) {
		$this->mstrAuthIdCode = $astrAuthIdCode;
	}

	function getRRN() {
		return $this->mstrRRN;
	}

	function setRRN( $astrRRN) {
		$this->mstrRRN = $astrRRN;
	}

	function getTxnType() {
		return $this->mstrTxnType;
	}

	function setTxnType($astrTxnType) {
		$this->mstrTxnType = $astrTxnType;
	}

	function setTxnDateTime($astrDateTime) {
		$this->mstrTxnDateTime = $astrDateTime;
	}

	function getTxnDateTime() {
		return($this->mstrTxnDateTime);
	}



	function PGResponse(){

	#	//System.out.println("Received Response : " + strResponse);
	#	Hashtable oHashtable= new Hashtable();
	#	try{
	#		try {
	#			StringTokenizer oItems	= new StringTokenizer(URLDecoder.decode(strResponse),"&");
	#			while(oItems.hasMoreElements()) {
	#				try {
	#					String strTokenData = oItems.nextToken();
	#					StringTokenizer oItems1 = new StringTokenizer(strTokenData,"=");
	#					String strToken = oItems1.nextToken();
	#					String strData = oItems1.nextToken();
	#					oHashtable.put(strToken, strData);
	#				}
	#				catch(Exception Exception) {
	#				}
	#			}
	#		}
	#		catch(Exception oException) {
	#		}
	#		$this->mstrRespCode = (String)oHashtable.get(PG_RESP_RESPCODE);
	#		$this->mstrRespMessage = (String)oHashtable.get(PG_RESP_RESPMSG);
	#		$this->mstrTxnId = (String)oHashtable.get(PG_RESP_MRT_TXN_ID);
	#		$this->mstrEPGTxnId = (String)oHashtable.get(PG_RESP_EPG_TXN_ID);
	#		$this->mstrRedirectionTxnId = (String)oHashtable.get(PG_RESP_REDIRECT_TXN_ID);
	#		$this->mstrAuthIdCode=(String)oHashtable.get(PG_RESP_AUTH_ID);
	#		$this->mstrRRN=(String)oHashtable.get(PG_RESP_RRN);
	#		$this->mstrTxnType=(String)oHashtable.get(PG_RESP_TXNTYPE);
	##		$this->mstrTxnDateTime = (String)oHashtable.get(PG_RESP_TXN_DATE_TIME);
	#	}catch(Exception oEx){
	#	}



	}

	function toString() {
		return("RespCode: " . $this->mstrRespCode . "\n".
			"RespMessage : " . $this->mstrRespMessage . "\n" .
			"RedirectionTxnId : " . $this->mstrRedirectionTxnId. "\n" .
			"TxnId : " . $this->mstrTxnId . "\n" .
			"EPGTxnId : " . $this->mstrEPGTxnId . "\n" .
			"AuthIdCode : " . $this->mstrAuthIdCode. "\n" .
			"RRN : " . $this->mstrRRN . "\n" .
			"TxnType : " . $this->mstrTxnType ."\n".
			"TxnDateTime : " . $this->mstrTxnDateTime. "\n".
			"CVRespCode : " . $this->mstrCVRespCode ."\n".
			"Reserve1 : " . $this->mstrReserveFld1 ."\n".
			"Reserve2:" . $this->mstrReserveFld1 ."\n".
			"Reserve3:" . $this->mstrReserveFld1 ."\n".
			"Reserve4:" . $this->mstrReserveFld1 ."\n".
			"Reserve5:" . $this->mstrReserveFld1 ."\n".
			"Reserve6:" . $this->mstrReserveFld1 ."\n".
			"Reserve7:" . $this->mstrReserveFld1 ."\n".
			"Reserve8:" . $this->mstrReserveFld1 ."\n".
			"Reserve9:" . $this->mstrReserveFld1 ."\n".
			"Reserve10:" . $this->mstrReserveFld1 ."\n");

	}
	function getResponse($retData) {
		$oPGResphp	=	new	PGResponse();
		$retData = trim($retData);
		parse_str($retData, $output);

		if( array_key_exists('RespCode', $output) == 1) {
			$oPGResphp->setRespCode($output['RespCode']);
		}
		if( array_key_exists('Message', $output) == 1) {
			$oPGResphp->setRespMessage($output['Message']);
		}
		if( array_key_exists('TxnID', $output) == 1) {
			$oPGResphp->setTxnId($output['TxnID']);
		}
		if( array_key_exists('RedirectionTxnID', $output) == 1) {
			$oPGResphp->setRedirectionTxnId($output['RedirectionTxnID']);
		}
		if( array_key_exists('ePGTxnID', $output) == 1) {
			$oPGResphp->setEpgTxnId($output['ePGTxnID']);
		}
		if( array_key_exists('AuthIdCode', $output) == 1) {
			$oPGResphp->setAuthIdCode($output['AuthIdCode']);
		}
		if( array_key_exists('RRN', $output) == 1) {
			$oPGResphp->setRRN($output['RRN']);
		}
		if( array_key_exists('TxnType', $output) == 1) {
			$oPGResphp->setTxnType($output['TxnType']);
		}
		if( array_key_exists('TxnDateTime', $output) == 1) {
			$oPGResphp->setTxnDateTime($output['TxnDateTime']);
		}
		if( array_key_exists('CVRespCode', $output) == 1) {
			$oPGResphp->setCVRespCode($output['CVRespCode']);
		}

		if( array_key_exists('Reserve1', $output) == 1) {
			$oPGResphp->setReserveFld1($output['Reserve1']);
		}

		if( array_key_exists('Reserve2', $output) == 1) {
			$oPGResphp->setReserveFld2($output['Reserve2']);
		}
		if( array_key_exists('Reserve3', $output) == 1) {
			$oPGResphp->setReserveFld3($output['Reserve3']);
		}

		if( array_key_exists('Reserve4', $output) == 1) {
			$oPGResphp->setReserveFld4($output['Reserve4']);
		}

		if( array_key_exists('Reserve5', $output) == 1) {
			$oPGResphp->setReserveFld5($output['Reserve5']);
		}
		if( array_key_exists('Reserve6', $output) == 1) {
			$oPGResphp->setReserveFld6($output['Reserve6']);
		}

		if( array_key_exists('Reserve7', $output) == 1) {
			$oPGResphp->setReserveFld7($output['Reserve7']);
		}
		if( array_key_exists('Reserve8', $output) == 1) {
			$oPGResphp->setReserveFld8($output['Reserve8']);
		}

		if( array_key_exists('Reserve9', $output) == 1) {
			$oPGResphp->setReserveFld9($output['Reserve9']);
		}

		if( array_key_exists('Reserve10', $output) == 1) {
			$oPGResphp->setReserveFld10($output['Reserve10']);
		}
		return $oPGResphp;
	}


}

?>
