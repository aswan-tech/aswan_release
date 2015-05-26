<?php

class FCM_Provider_Block_Adminhtml_Provider_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset("provider_form", array("legend" => Mage::helper("provider")->__("Provider Information")));

        $fieldset->addField('shippingprovider_name', 'select', array(
            'label' => Mage::helper('adminhtml')->__('Shipping Provider Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'shippingprovider_name',
            'onclick' => "",
            'onchange' => "",
            'values' => Mage::getModel("provider/provider")->getDropDownOptions(),
            'disabled' => false,
            'readonly' => false,
            'tabindex' => 1
        ));

        $fieldset->addField('shippingprovider_hovertext', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Shipping Provider Hover Text'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'shippingprovider_hovertext',
            'tabindex' => 2
        ));

        $fieldset->addField('shippingprovider_action', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Shipping Provider Action'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'shippingprovider_action',
            'tabindex' => 3
        ));

        if (Mage::getSingleton("adminhtml/session")->getProviderData()) {
            $form->setValues(Mage::getSingleton("adminhtml/session")->getProviderData());
            Mage::getSingleton("adminhtml/session")->setProviderData(null);
        } elseif (Mage::registry("provider_data")) {
            $form->setValues(Mage::registry("provider_data")->getData());
        }
        return parent::_prepareForm();
    }

}