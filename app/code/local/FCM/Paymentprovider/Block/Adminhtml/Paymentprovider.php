<?php

class FCM_Paymentprovider_Block_Adminhtml_Paymentprovider extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = "adminhtml_paymentprovider";
        $this->_blockGroup = "paymentprovider";
        $this->_headerText = Mage::helper("paymentprovider")->__("Manage Payment Providers");
        $this->_addButtonLabel = Mage::helper("paymentprovider")->__("Add Payment Provider");
        parent::__construct();
    }

}