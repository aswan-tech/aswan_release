<?php

class MPIData
{

   ##
    #
    # Unformatted purchase amount. It should not contain any special characters such as "$" etc.
    #
    #

	 var  $mstrPurchaseAmount ;
   ##
    #
    # Formatted purchase amount. This field should contain a currency symbol, with an thousands separator (s), decimal point and ISO minor units defined for the currency specified in the Purchase Currency field.
    #		Ex. $1,234.56
    #
    #/

	var $mstrDisplayAmount;
   ##
    #
    # Contains the Standard ISO currency value for the country. The value for India is 356. For details refer:
    # http://userpage.chemie.fu-berlin.de/diverse/doc/ISO_3166.html
    #
    #/

	var $mstrCurrencyVal;
   ##
    #
    # 	The number of decimal places used in the amount is specified. Used to distinguish the units of money like Rs and Ps.
    #   Example: Amount 100000 with an exponent value of 2 becomes Rs.1,000
    #
    #/

	var $mstrExponent;
   ##
    #
    # Brief description of items purchased, determined by the Merchant.
    #	Example 2 shirts.

    #
    #

	var $mstrOrderDesc;
   ##
    #
    # This field is calculated based on installments and the Recur End and it denotes the frequency of payment. It is optional and must be included if other recurring variables are specified.
    #
    #

	var $mstrRecurFreq;
   ##
    #
    # This field indicates the end date of recurring value. It should be less than the card expiry date. It is also an optional field.
    #
    #

	var $mstrRecurEnd;
   ##
    #
    # MPI supports payment by installments. In order to enable this support to the customer, the following fields have to be set.This field indicates the number of times the payment is done to fulfill the transaction.
    #
    #

	var $mstrInstallment;
   ##
    #
    # This attribute indicates mode of transaction. For an internet based transaction the value to be set is "0".
    #
    #

	var $mstrDeviceCategory;
   ##
    # Denotes the browser version of the client. This field can be empty and is used by the MPI to denote the browser version.
    #
    #
    #

	var $mstrWhatIUse;
   ##
    #
    # The Accept request-header field can be used to specify certain media types which are acceptable for the response. Accept headers can be used to indicate that the request is specifically limited to a small set of desired types, as in the case of a request for an in-line image.
    # The server property request.getHeader("Accept") can be used for setting this value.

    #
    #

	var $mstrAcceptHdr;
   ##
    #
    # The User-Agent-header contains information about the user agent
    #(typically a newsreader) generating the article, for statistical
    #purposes and tracing of standards violations to specific software
    #needing correction. Although not one of the mandatory headers,
    #posting agents SHOULD normally include it.
    #The server property request.getHeader("User-Agent") can be used for setting this value.

    #
    #

	var $mstrAgentHdr;
        ##
	#	Any Echo back field value
	#
	var $mstrShoppingContext;
   ##
    #
    # Should be set to the result of conducting an MPI transaction
    #
    #

	var $mstrVBVStatus;
   ##
    # Should be set to the result of conducting an MPI transaction
    #
    #

	var $mstrCAVV;
   ##
    # Should be set to the result of conducting an MPI transaction
    #
    #

	var $mstrECI;
   ##
    # Should be set to the result of conducting an MPI transaction
    #
    #

	var $mstrXID;

      # Default constructor of class MPIData
        function MPIData()
        {
        
        }



	function getECI()
	{
		return $this->mstrECI;
	}

	function getXID()
	{
		return $this->mstrXID;
	}

	function getVBVStatus()
	{
		return $this->mstrVBVStatus;
	}

	function setMPIRequestDetails($astrPurchaseAmount, $astrDisplayAmount, $astrCurrencyVal,
					$astrExponent, $astrOrderDesc, $astrRecurFreq,$astrRecurEnd, $astrInstallment,
					$astrDeviceCategory, $astrWhatIUse, $astrAcceptHdr, $astrAgentHdr
									)
	{
        $this->mstrPurchaseAmount = $astrPurchaseAmount;
        $this->mstrDisplayAmount = $astrDisplayAmount;
        $this->mstrCurrencyVal = $astrCurrencyVal;
        $this->mstrExponent = $astrExponent;
        $this->mstrOrderDesc = $astrOrderDesc;
        $this->mstrRecurFreq = $astrRecurFreq;
        $this->mstrRecurEnd = $astrRecurEnd;
        $this->mstrInstallment = $astrInstallment;
        $this->mstrDeviceCategory = $astrDeviceCategory;
        $this->mstrWhatIUse = $astrWhatIUse;
        $this->mstrAcceptHdr = $astrAcceptHdr;
        $this->mstrAgentHdr = $astrAgentHdr;

    }

	function setMPIResponseDetails( $astrECI,$astrXID,$astrVBVStatus,
					$astrCAVV,$astrShoppingContext,
					$astrPurchaseAmount,$astrCurrencyVal)
	{
        $this->mstrECI = $astrECI;
        $this->mstrXID = $astrXID;
        $this->mstrVBVStatus = $astrVBVStatus;
        $this->mstrCAVV = $astrCAVV;
        $this->mstrShoppingContext=$astrShoppingContext;
    	$this->mstrPurchaseAmount =$astrPurchaseAmount;
        $this->mstrCurrencyVal =$astrCurrencyVal;
    }


	function getCAVV()
	{
		return $this->mstrCAVV;
	}


	function getPurchaseAmount()
	{
   	     return $this->mstrPurchaseAmount;
    	}

	function getDisplayAmount()
	{
   	     return $this->mstrDisplayAmount;
    	}

	function getCurrencyVal()
	{
   	     return $this->mstrCurrencyVal;
    	}

	function getExponent()
	{
   	     return $this->mstrExponent;
    	}

	function getOrderDesc()
	{
   	     return $this->mstrOrderDesc;
    	}

	function getRecurFreq()
	{
   	     return $this->mstrRecurFreq;
    	}

	function getRecurEnd()
	{
   	     return $this->mstrRecurEnd;
    	}

	function getInstallment()
	{
   	     return $this->mstrInstallment;
    	}

        function getDeviceCategory()
	{
   	     return $this->mstrDeviceCategory;
    	}

	function getWhatIUse()
	{
   	     return $this->mstrWhatIUse;
    	}

	function getAcceptHdr()
	{
   	     return $this->mstrAcceptHdr;
    	}

	function getAgentHdr()
	{
   	     return $this->mstrAgentHdr;
    	}

	function getShoppingContext()
	{
   	     return $this->mstrShoppingContext;
    	}
	
	function toString()
	{
		print "Purchase Amount".$this->mstrPurchaseAmount."\n".  
		      "Display Amount".        $this->mstrDisplayAmount ."\n".
		      "Currency  ".	        $this->mstrCurrencyVal  ."\n".
		      "Exponent  ".	        $this->mstrExponent ."\n".
		      "Order Desc".	        $this->mstrOrderDesc ."\n".
		      "RecurFreq ".	        $this->mstrRecurFreq ."\n".
		      "Recur End".	        $this->mstrRecurEnd ."\n".
		      "Installment".	        $this->mstrInstallment ."\n".
		      "Device Category".        $this->mstrDeviceCategory."\n".
		      "What i use".	        $this->mstrWhatIUse ."\n".
		      "Accept header".          $this->mstrAcceptHdr ."\n".
		      "Header Agent".	        $this->mstrAgentHdr ."\n" ;

	
	}

}

?>