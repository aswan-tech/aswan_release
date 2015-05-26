<?php

class FCM_Paymentprovider_Block_Adminhtml_Paymentprovider_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = "id";
        $this->_blockGroup = "paymentprovider";
        $this->_controller = "adminhtml_paymentprovider";

        $this->_updateButton("save", "label",
                Mage::helper("paymentprovider")->__("Save Provider"));
        $this->_updateButton("delete", "label",
                Mage::helper("paymentprovider")->__("Delete Provider"));
    }

    public function getHeaderText() {
        if (Mage::registry("paymentprovider_data") &&
                Mage::registry("paymentprovider_data")->getPaymentId()) {
            return Mage::helper("paymentprovider")->__("Edit Provider '%s'", $this->htmlEscape(Mage::registry("paymentprovider_data")->getPaymentMethodName()));
        } else {
            return Mage::helper("paymentprovider")->__("Add Provider");
        }
    }

}