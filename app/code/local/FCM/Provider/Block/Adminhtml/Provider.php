<?php
class FCM_Provider_Block_Adminhtml_Provider
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = "adminhtml_provider";
        $this->_blockGroup = "provider";
        $this->_headerText = Mage::helper("provider")->__("Manage Shipping Providers");
        $this->_addButtonLabel = Mage::helper("provider")->__("Add Provider");
        parent::__construct();
    }
}