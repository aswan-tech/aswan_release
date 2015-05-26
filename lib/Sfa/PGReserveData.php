
<?php

##
#
# PGReserveData class is used to set the details of the Reserved fields. The method setReservedObject is used for the purpose.
#
#

 class PGReserveData
{

   var $mstrReserveField1;
   var $mstrReserveField2;
   var $mstrReserveField3;
   var $mstrReserveField4;
   var $mstrReserveField5;
   var $mstrReserveField6;
   var $mstrReserveField7;
   var $mstrReserveField8;
   var $mstrReserveField9;
   var $mstrReserveField10;



  #default constructor of PGReserveData class
	function PGReserveData()
	{
	}

    function setReserveObj($astrReserveField1,$astrReserveField2,$astrReserveField3,$astrReserveField4,
    				$astrReserveField5,$astrReserveField6, $astrReserveField7,$astrReserveField8,
    				$astrReserveField9,$astrReserveField10)
    {
			$this->mstrReserveField1 = $astrReserveField1;
			$this->mstrReserveField2 = $astrReserveField2;
			$this->mstrReserveField3 = $astrReserveField3;
			$this->mstrReserveField4 = $astrReserveField4;
			$this->mstrReserveField5 = $astrReserveField5;
			$this->mstrReserveField6 = $astrReserveField6;
			$this->mstrReserveField7 = $astrReserveField7;
			$this->mstrReserveField8 = $astrReserveField8;
			$this->mstrReserveField9 = $astrReserveField9;
			$this->mstrReserveField10 = $astrReserveField10;

    }



	function getReserveField1(){
		return $this->mstrReserveField1;
	}

	function setReserveField1($astrReserveField1){
		$this->mstrReserveField1= $astrReserveField1;
	}

	function getReserveField2(){
			return $this->mstrReserveField2;
	}

	function setReserveField2($astrReserveField2){
			$this->mstrReserveField2= $astrReserveField2;
	}


	function getReserveField3(){
			return $this->mstrReserveField3;
	}

	function setReserveField3($astrReserveField3){
			$this->mstrReserveField3= $astrReserveField3;
	}


	function getReserveField4(){
			return $this->mstrReserveField4;
	}

	function setReserveField4($astrReserveField4){
			$this->mstrReserveField4= $astrReserveField4;
	}

	function getReserveField5(){
			return $this->mstrReserveField5;
	}

	function setReserveField5($astrReserveField5){
			$this->mstrReserveField5= $astrReserveField5;
	}


	function getReserveField6(){
			return $this->mstrReserveField6;
	}

	function setReserveField6($astrReserveField6){
			$this->mstrReserveField6= $astrReserveField6;
	}


	function getReserveField7(){
			return $this->mstrReserveField7;
	}

	function setReserveField7($astrReserveField7){
			$this->mstrReserveField7= $astrReserveField7;
	}


	function getReserveField8(){
			return $this->mstrReserveField8;
	}

	function setReserveField8($astrReserveField8){
			$this->mstrReserveField8= $astrReserveField8;
	}


	function getReserveField9(){
			return $this->mstrReserveField9;
	}

	function setReserveField9($astrReserveField9){
			$this->mstrReserveField9= $astrReserveField9;
	}


	function getReserveField10(){
			return $this->mstrReserveField10;
	}

	function setReserveField10($astrReserveField10){
			$this->mstrReserveField10= $astrReserveField10;
	}



}

?>