<?php

class FCM_Paymentprovider_Block_Adminhtml_Paymentprovider_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId("paymentprovider_tabs");
        $this->setDestElementId("edit_form");
        $this->setTitle(Mage::helper("paymentprovider")->__("Payment Provider Information"));
    }

    protected function _beforeToHtml() {
        $this->addTab("form_section", array(
            "label" => Mage::helper("paymentprovider")->__("Payment Provider Information"),
            "title" => Mage::helper("paymentprovider")->__("Payment Provider Information"),
            "content" => $this->getLayout()->createBlock("paymentprovider/adminhtml_paymentprovider_edit_tab_form")->toHtml(),
        ));

        return parent::_beforeToHtml();
    }

}