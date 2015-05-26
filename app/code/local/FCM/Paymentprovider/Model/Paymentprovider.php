<?php

class FCM_Paymentprovider_Model_Paymentprovider extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init("paymentprovider/paymentprovider");
    }

}