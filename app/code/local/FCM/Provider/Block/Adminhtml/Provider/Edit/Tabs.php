<?php
class FCM_Provider_Block_Adminhtml_Provider_Edit_Tabs
    extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId("provider_tabs");
        $this->setDestElementId("edit_form");
        $this->setTitle(Mage::helper("provider")->__("Shipping Provider Information"));
    }

    protected function _beforeToHtml()
    {
        $this->addTab("form_section", array(
                          "label" => Mage::helper("provider")->__("Provider Information"),
                          "title" => Mage::helper("provider")->__("Provider Information"),
                          "content" => $this->getLayout()->createBlock("provider/adminhtml_provider_edit_tab_form")->toHtml(),
                      ));

        return parent::_beforeToHtml();
    }
}