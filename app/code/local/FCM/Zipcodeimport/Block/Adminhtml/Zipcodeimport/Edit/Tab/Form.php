<?php

class FCM_Zipcodeimport_Block_Adminhtml_Zipcodeimport_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('zipcodeimport_form', array('legend' => Mage::helper('zipcodeimport')->__('Zip Code information')));

        $fieldset->addField('zip_code', 'text', array(
            'label' => Mage::helper('zipcodeimport')->__('Zip Code'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'zip_code',
            'disabled' => true
        ));

        $fieldset->addField('state', 'text', array(
            'label' => Mage::helper('zipcodeimport')->__('State'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'state',
        ));

        $fieldset->addField('city', 'text', array(
            'label' => Mage::helper('zipcodeimport')->__('City'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'city',
        ));

        $fieldset->addField('express', 'text', array(
            'label' => Mage::helper('zipcodeimport')->__('Express'),
            'class' => 'required-entry validate-number',
            'required' => true,
            'name' => 'express',
        ));


        $fieldset->addField('standard', 'text', array(
            'label' => Mage::helper('zipcodeimport')->__('Standard'),
            'class' => 'required-entry  validate-number',
            'required' => true,
            'name' => 'standard',
        ));


        $fieldset->addField('appointment', 'text', array(
            'label' => Mage::helper('zipcodeimport')->__('Appointment'),
            'class' => 'required-entry  validate-number',
            'required' => true,
            'name' => 'appointment',
        ));


        $fieldset->addField('overnite', 'text', array(
            'label' => Mage::helper('zipcodeimport')->__('Overnite'),
            'class' => 'required-entry  validate-number',
            'required' => true,
            'name' => 'overnite',
        ));


        $fieldset->addField('cod', 'text', array(
            'label' => Mage::helper('zipcodeimport')->__('COD'),
            'class' => 'required-entry  validate-number',
            'required' => true,
            'name' => 'cod',
        ));

        $fieldset->addField('blinkecarrier_id', 'text', array(
            'label' => Mage::helper('zipcodeimport')->__('Blinke Carrier Id'),
            'class' => '',
            'required' => '',
            'name' => 'blinkecarrier_id',
            'disabled' => true
        ));



        if (Mage::getSingleton('adminhtml/session')->getZipcodeimportData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getZipcodeimportData());
            Mage::getSingleton('adminhtml/session')->setZipcodeimportData(null);
        } elseif (Mage::registry('zipcodeimport_data')) {
            $form->setValues(Mage::registry('zipcodeimport_data')->getData());
        }
        return parent::_prepareForm();
    }

}