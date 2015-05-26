<?php


 
 class CardInfo {
 
 ##
     #
     # The merchant has to send the card type selected by the customer for the transaction. The valid values for this are
     # VISA,MC,AMEX.JCB
     #
 
 	 var $mstrCardType ;
   ##
     # Card number given by the customer to process the transaction.
     #
     #
     #
 
 	 var $mstrCardNum;
  ##
     # Card Expiration year of the Card type selected should be specified. The format is yy
     #
     #
     #
 
 	 var $mstrExpDtYr;
  ##
     # Card Expiration month of the Card type selected should be specified. The format is mm
     #
     #
     #
 
 	 var $mstrExpDtMon;
  ##
     # Card Verification value on the card that the customer supplies in the payment page.
     #
     #
     #
 
 	 var $mstrCVVNum;
   ##
     # Name on card corresponds to the name given by the customer in the payment page.
     #
     #
     #
 
 	 var $mstrNameOnCard;
    ##
     # Credit or Debit card selected by the customer in the payment page.
     #
     #
     #
     
      var $mstrInstrType;
 	
    ## constructor of CardInfo class without any arguments 
      function CardInfo(){
      }
	
	
	function  setCardDetails( $astrCardType, $astrCardNum, $astrCVVNum, $astrExpDtYr, $astrExpDtMon, $astrNameOnCard, $astrInstrType)
		{
			$this->mstrCardType = $astrCardType;
			$this->mstrCardNum = $astrCardNum;
			$this->mstrCVVNum = $astrCVVNum;
			$this->mstrExpDtYr = $astrExpDtYr;
			$this->mstrExpDtMon = $astrExpDtMon;
			$this->mstrNameOnCard = $astrNameOnCard;
			$this->mstrInstrType = $astrInstrType;
		}
	
	function getCardType()
		{
			return $this->mstrCardType;
		}
	
	function getCardNum()
		{
			return $this->mstrCardNum;
		}
	
	function getCVVNum()
	    	{
			return $this->mstrCVVNum;
		}
	
	function getExpDtYr()
		{
			return $this->mstrExpDtYr ;
		}
	
	function getExpDtMon()
		{
			return $this->mstrExpDtMon ;
		}
	
	function getNameOnCard()
		{
			return $this->mstrNameOnCard;
		}
	
	function getInstrType()
		{
			return $this->mstrInstrType;
		}
	
	function toString()
		{
			return "The Card Details are \n"+
					"CardType   	 "+$mstrCardType +"\n"+
					"CardNumber 	 "+$mstrCardNum+"\n"+
					"CV Num		 "+$mstrCVVNum+"\n"+
					"Card Name	 "+$mstrNameOnCard;
		}
	 
   	
   	
   	
   	
   	
   	function cardprint()
   	 {
   	  print " Inside Card info class"   ;
   	 }
	 
	 
 }
?>