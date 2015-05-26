
<?php


class ShipToAddress 
{
		var $mstrCountryAlphaCode;
		var $mstrAddLine1 ;
		var $mstrAddLine2 ;
		var $mstrAddLine3 ;
		var $mstrCity;
		var $mstrState;
		var $mstrZip;
		var $mstrEmail;
	
		function setAddressDetails($astrAddrLine1, $astrAddrLine2, $astrAddrLine3, $astrCity, $astrState, $astrZip, $astrCountryAlpha,$astrEmail)
		{
			$this->mstrAddLine1 = $astrAddrLine1 ;
			$this->mstrAddLine2 = $astrAddrLine2 ;
			$this->mstrAddLine3 = $astrAddrLine3 ;
			$this->mstrCity=$astrCity;
			$this->mstrState=$astrState;
			$this->mstrZip = $astrZip;
			$this->mstrCountryAlphaCode = $astrCountryAlpha;
			$this->mstrEmail	=	$astrEmail;
		}
	
		function getAddrLine1()
		{
			return $this->mstrAddLine1 ;
		}
	
		function getAddrLine2()
		{
			return $this->mstrAddLine2 ;
		}
	
		function getAddrLine3()
		{
			return $this->mstrAddLine3 ;
		}
	
		function getCity()
		{
			return $this->mstrCity;
		}
	
		function getState()
		{
			return $this->mstrState;
		}
	
		function getZip()
		{
			return $this->mstrZip;
		}
	
		function getCountryAlphaCode()
		{
			return $this->mstrCountryAlphaCode;
		}
		
		function getEmail(){
			return $this->mstrEmail;
		}

	# Default constructor of ShiptoAddress  class
	function ShipToAddress()
	{
	
	}
	
	function toString()
	{
		return "The Ship  address is \n".
				"Street		".$this->mstrAddLine1."\n".
				"        	 ".$this->mstrAddLine2."\n".
				"       	 ".$this->mstrAddLine3 ."\n".
				"City		 ".$this->mstrCity."\n".
				"State	 	 ".$this->mstrState."\n".
				"Zip		 ".$this->mstrZip."\n".
				"CountryAlphaCode ".$this->mstrCountryAlphaCode;
	}
}

?>