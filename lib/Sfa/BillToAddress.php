<?php
class BillToAddress
{

	var $mstrEmail;
	var $mstrCustomerId;
	var $mstrCustomerName;
	var $mstrCountryAlphaCode;
	var $mstrAddLine1 ;
	var $mstrAddLine2 ;
	var $mstrAddLine3 ;
	var $mstrCity;
	var $mstrState;
	var $mstrZip;




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


	#default constructor of  billtoaddress
	function BillToAddress()
	{

	}


	function setAddressDetails($astrCustomerId, $astrCustomerName,$astrAddrLine1, $astrAddrLine2, $astrAddrLine3,$astrCity, $astrState, $astrZip, $astrCountryAlpha, $astrEmail)
	{
			$this->mstrCustomerId=$astrCustomerId;
			$this->mstrCustomerName=$astrCustomerName;

			$this->mstrAddLine1 = $astrAddrLine1 ;
			$this->mstrAddLine2 = $astrAddrLine2 ;
			$this->mstrAddLine3 = $astrAddrLine3 ;
			$this->mstrCity=$astrCity;
			$this->mstrState=$astrState;
			$this->mstrZip = $astrZip;
			$this->mstrCountryAlphaCode = $astrCountryAlpha;
			$this->mstrEmail = $astrEmail;

	}

	function getEmail()
	{
		return $this->mstrEmail;
	}

	function getCustomerId()
	{
		return $this->mstrCustomerId;
	}

	function getName()
	{
		return $this->mstrCustomerName;
	}


	function toString()
	{
			return "The Bill to address is \n".
					"CustomerId 	 ".$this->mstrCustomerId."\n".
					"CustomerName	 ".$this->mstrCustomerName."\n".
					"Street   	 ".$this->mstrAddLine1 ."\n".
					"        	 ".$this->mstrAddLine2 ."\n".
					"       	 ".$this->mstrAddLine3 ."\n".
					"City		 ".$this->mstrCity."\n".
					"State	 	 ".$this->mstrState."\n".
					"Zip		 ".$this->mstrZip."\n".
					"CountryAlphaCode".$this->mstrCountryAlphaCode."\n".
					"Email	 	 ".$this->mstrEmail;
	}
}


?>