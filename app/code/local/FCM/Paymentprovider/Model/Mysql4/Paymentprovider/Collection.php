<?php

class FCM_Paymentprovider_Model_Mysql4_Paymentprovider_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {
        $this->_init("paymentprovider/paymentprovider");
    }

}