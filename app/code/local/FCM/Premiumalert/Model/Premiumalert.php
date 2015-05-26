<?php

class FCM_Premiumalert_Model_Premiumalert extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('premiumalert/premiumalert');
    }
	
	public function toOptionArray()
	{
		$thresholdvalue = array();
		for($i=1;$i<=100;$i++){
				$thresholdvalue[$i] = array('value' => ''.$i.'', 'label'=>''.$i.'');
		}
		return $thresholdvalue;		
	}
}