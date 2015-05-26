<?php
class FCM_Paymentprovider_Model_Mysql4_Paymentprovider extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init("paymentprovider/paymentprovider", "payment_id");
    }
}