
<?php

##
#
# Merchant class is used to set the details of the Merchant,Invoice and extra fields. The method setMerchantDetails is used for the purpose.
#
#

 class Merchant
{
    #Request message header description fields
   ##
    # Merchant ID corresponds to the ID given by the gateway to the merchant..A string value is passed to the gateway
    #
    #
    #

   var $mstrMerchantID;


   ##
    #
    # This corresponds to the unique number for the order at the shopping cart of the merchant.
    #
    #

    var	$mstrOrderReferenceNo;

   ##
    #
    # This corresponds to the unique number for the transaction initiated by the merchant from his site.
    #
    #

    var $mstrMerchantTxnID;



   ##
    #
    # In case the Merchant supports products from multiple vendors, a unique number identifying the vendor has to be given here..A string value is passed to the gateway
    #
    #
	var $mstrVendor;

   ##
    #
    #Corresponds to a unique number of the Parner.A string value is passed to the gateway
    #
    #

	var $mstrPartner;

   ##
    #
    # Denotes the URL to which the Payment Gateway redirects the result of the transaction.
    #
    #

	var $mstrRespURL;

   ##
    #
    #Denotes the method used by the Payment Gateway to redirect the result of the transaction.
    #
    #

	var  $mstrRespMethod;

   ##
    #
    #Any other external field that the user wishes to supply along with the transaction.
    #
    #

	var	$mstrExt1;

   ##
    #
    #Any other external field that the user wishes to supply along with the transaction.
    #
    #

	var 	$mstrExt2;

   ##
    #
    #Any other external field that the user wishes to supply along with the transaction.
    #
    #

	var	$mstrExt3;

   ##
    #
    #Any other external field that the user wishes to supply along with the transaction.
    #
    #

	var 	$mstrExt4;

   ##
    #
    #Any other external field that the user wishes to supply along with the transaction.
    #
    #

	var 	$mstrExt5;

   ##
    #
    #The standard ISO currency code for the country has to specified.
    #
    #

	var	$mstrCurrCode;


   ##
    #
    # The message type(req.Preauthorization,req.Sale) that corresponds to the transaction that the merchant wishes to conduct.
    #
    #

	var	$mstrMessageType;

   ##
    #
    # The GMT Offset of the client
    #
    #

	var	$mstrGMTTimeOffset;

   ##
    #
    # This corresponding to a invoice number given by the merchant to the customer.
    #
    #

	var     $mstrInvoiceNo;

   ##
    #
    # The timestamp when the merchant has sent the transaction to the Gateway.
    #
    #

	var	$moInvoiceDate;

   ##
    #
    # The transaction amount for the invoice.
    #
    #

	var	$mstrAmount;

   ##
    #
    # The merchant captures the IPAddress in case he is a Moto Merchant and sends it in this parameter. This can be left blank in case of a SSL merchant as the payment gateway captures these details.
    #
    #

	var $mstrCustIPAddress;

    ##
	 #
	 # Transaction Reference Number of the orignal transaction
	 #
     #

    var $mstrRootTxnSysRefNum;

    ##
	 #
	 # RRN Number of the orignal transaction
	 #
     #
    var $mstrRootPNRefNum;

    ##
	 #
	 #  Auth id of the orignal transaction
	 #
     #
    var $mstrRootAuthCode;

    ##
	 #
	 #  Language code used by SFA
	 #
	 #
    var $mstrLanguageCode;

    ##
	 #
	 #  Start date for Searching Transaction through SFA
	 #
	 #
    var $mstrStartDate;

    ##
	 #
	 #  End date for Searching Transaction through SFA
	 #
	 #
    var $mstrEndDate;

  #default constructor of Merchant class
	function Merchant()
	{
	}

    function setMerchantDetails($astrMerchantID,$astrVendor , $astrPartner,$astrCustIPAddress,
    				$astrMerchantTxnID,$astrOrderReferenceNo, $astrRespURL,
    				$astrRespMethod,$astrCurrCode,$astrInvoiceNo, $astrMessageType,
    				$astrAmount,$astrGMTTimeOffset,$astrExt1, $astrExt2,
    				$astrExt3, $astrExt4, $astrExt5)
    {
			$this->mstrMerchantID = $astrMerchantID;
			$this->mstrVendor = $astrVendor;
			$this->mstrPartner = $astrPartner;
			$this->mstrCustIPAddress = $astrCustIPAddress;
			$this->mstrMerchantTxnID =$astrMerchantTxnID;
			$this->mstrOrderReferenceNo = $astrOrderReferenceNo;
			$this->mstrRespURL = $astrRespURL;
			$this->mstrRespMethod = $astrRespMethod;
			$this->mstrCurrCode = $astrCurrCode;
			$this->mstrInvoiceNo= $astrInvoiceNo;
			$this->mstrMessageType = $astrMessageType;
			$this->mstrAmount = $astrAmount;
			$this->mstrGMTTimeOffset= $astrGMTTimeOffset;
			$this->mstrExt1 	= $astrExt1;
			$this->mstrExt2 	= $astrExt2;
			$this->mstrExt3 	= $astrExt3;
			$this->mstrExt4 	= $astrExt4;
			$this->mstrExt5 	= $astrExt5;

    }

    function setMerchantRelatedTxnDetails($astrMerchantID,$astrVendor ,$astrPartner,
    					  $astrMerchantTxnID,$astrRootTxnSysRefNum,
    					  $astrRootPNRef, $astrRootAuthCode,
	    				  $astrRespURL, $astrRespMethod, $astrCurrCode,
	    				  $astrMessageType, $astrAmount,$astrGMTTimeOffset,
	    				  $astrExt1, $astrExt2, $astrExt3, $astrExt4, $astrExt5)
	{
				$this->mstrMerchantID = $astrMerchantID;
				$this->mstrPartner = $astrPartner;
				$this->mstrMerchantTxnID	=	$astrMerchantTxnID;
				$this->mstrRootTxnSysRefNum=   $astrRootTxnSysRefNum;
				$this->mstrRootPNRefNum	=   $astrRootPNRef;
				$this->mstrRootAuthCode	=   $astrRootAuthCode;
				$this->mstrRespURL = $astrRespURL;
				$this->mstrRespMethod = $astrRespMethod;
				$this->mstrCurrCode = $astrCurrCode;
				$this->mstrMessageType = $astrMessageType;
				$this->mstrAmount = $astrAmount;
				$this->mstrGMTTimeOffset=$astrGMTTimeOffset;
				$this->mstrExt1 	= $astrExt1;
				$this->mstrExt2 	= $astrExt2;
				$this->mstrExt3 	= $astrExt3;
				$this->mstrExt4 	= $astrExt4;
				$this->mstrExt5 	= $astrExt5;
				$this->mstrVendor = $astrVendor;


    }

	function setMerchantOnlineInquiry($astrMerchantID,$astrMerchantTxnID){
		$this->mstrMerchantID = $astrMerchantID;
		$this->mstrMerchantTxnID=$astrMerchantTxnID;

	}

	function setMerchantTxnSearch($astrMerchantID,$astrStartDate,$astrEndDate){
			$this->mstrMerchantID = $astrMerchantID;
			$this->mstrStartDate = $astrStartDate;
			$this->mstrEndDate = $astrEndDate;
	}

	function getLanguageCode(){
		return $this->mstrLanguageCode;
	}

	function setLanguageCode($astrLanguageCode){
		$this->mstrLanguageCode= $astrLanguageCode;
	}
	function getStartDate(){
		return $this->mstrStartDate;
	}

	function setStartDate($astrStartDate){
		$this->mstrStartDate= $astrStartDate;
	}

	function getEndDate(){
		return $this->mstrEndDate;
	}

	function setEndDate($astrEndDate){
		$this->mstrEndDate= $astrEndDate;
	}

	function getMerchantID() {
		return $this->mstrMerchantID;
	}

	function getMrtIPAddress(){
		#	try{
		#	return InetAddress.getLocalHost().getHostAddress();
		#	}catch(Exception netExc){
		#	return null;
		#	}

		$ipaddress = $_SERVER["REMOTE_ADDR"];
		return $ipaddress;
	}

	function getCustIPAddress(){
		return $this->mstrCustIPAddress;
	}

	function getVendor()
	{
		return $this->mstrVendor;
	}

	function getPartner()
	{
		return $this->mstrPartner;
	}

	function getOrderReferenceNo()
	{
		return $this->mstrOrderReferenceNo;
	}

	function getRespURL()
	{
		return $this->mstrRespURL;
	}

	function getRespMethod()
	{
		return $this->mstrRespMethod;
	}

	function getCurrCode()
	{
		return $this->mstrCurrCode;
	}

	function getInvoiceNo()
	{
		return $this->mstrInvoiceNo;
	}

	function getInvoiceDate()
	{

		return mktime();
	}

	function getMerchantTxnID()
	{
		return $this->mstrMerchantTxnID;
	}

	function getMessageType()
	{
		return $this->mstrMessageType;
	}

	function getAmount()
	{
		return $this->mstrAmount;
	}

	function getGMTTimeOffset()
	{
		return $this->mstrGMTTimeOffset;
	}

	function getExt1()
	{
		return $this->mstrExt1;
	}

	function getExt2()
	{
		return $this->mstrExt2;
	}

	function getExt3()
	{
		return $this->mstrExt3;
	}

	function getExt4()
	{
		return $this->mstrExt4;
	}

	function getExt5()
	{
		return $this->mstrExt5;
	}
	function getRootTxnSysRefNum (){

		return $this->mstrRootTxnSysRefNum;
	}

	function getRootPNRefNum(){

		return $this->mstrRootPNRefNum;
	}

	function getRootAuthCode(){

		return $this->mstrRootAuthCode ;
	}

}

?>