<?php
class FCM_Provider_Block_Adminhtml_Provider_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = "id";
        $this->_blockGroup = "provider";
        $this->_controller = "adminhtml_provider";

        $this->_updateButton("save", "label",
                             Mage::helper("provider")->__("Save Provider"));
        $this->_updateButton("delete", "label",
                             Mage::helper("provider")->__("Delete Provider"));
    }

    public function getHeaderText()
    { 
        if (Mage::registry("provider_data") &&
            Mage::registry("provider_data")->getId()) {
            return Mage::helper("provider")->__("Edit Provider '%s'", $this->htmlEscape(Mage::registry("provider_data")->getShippingproviderName()));
        } else {
            return Mage::helper("provider")->__("Add Provider");
        }
    }
}