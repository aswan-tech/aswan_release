<?php
 class PGSearchResponse {

		var $mstrRespCode=null;
		var $mstrRespMessage=null;
		var $arrayPGResponseObjects=null;




	// default constructor
	function PGSearchResponse() {
	}

	function getRespCode(){
		return $this->mstrRespCode;
	}

	function setRespCode($astrRespCode) {
		$this->mstrRespCode = $astrRespCode;
	}

	function getRespMessage(){
		return $this->mstrRespMessage;
	}

	function setRespMessage($astrRespMessage) {
		$this->mstrRespMessage = $astrRespMessage;
	}

	function setPGResponseObjects($arrlist){
		$this->arrayPGResponseObjects = $arrlist;
	}

	function getPGResponseObjects(){
		return  $this->arrayPGResponseObjects;
	}
	function getResponse($retData) {
		$oPGSearchResphp	=	new	PGSearchResponse();
		$oPGResphp	=	new	PGResponse();
		$retData = trim($retData);
		$token = strtok($retData, "\n");
		$index = 1;
		while($token){
		  $oPGResphp  = $oPGResphp->getResponse($token);
		  $token = strtok("\n");
		  if ($index == 1) {
			$mstrRespCode = $oPGResphp->getRespCode();
			$mstrRespMessage = $oPGResphp->getRespMessage();

			if ($mstrRespCode == 0) {
				$arraylistphp = array();
			} else {
				$arraylistphp = array();
				array_push($arraylistphp , $oPGResphp);
			}
		  } else {
			  array_push($arraylistphp , $oPGResphp);
		  }
		  $index++;

		}
		$oPGSearchResphp->setPGResponseObjects($arraylistphp);
		return $oPGSearchResphp;
	}


}


?>