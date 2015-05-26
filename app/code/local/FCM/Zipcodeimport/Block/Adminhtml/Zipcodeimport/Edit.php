<?php

class FCM_Zipcodeimport_Block_Adminhtml_Zipcodeimport_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'zipcodeimport';
        $this->_controller = 'adminhtml_zipcodeimport';
        $this->_removeButton('delete');
        $this->_updateButton('save', 'label', Mage::helper('zipcodeimport')->__('Save Zip Code')); 
    }

    public function getHeaderText()
    {
        if( Mage::registry('zipcodeimport_data') && Mage::registry('zipcodeimport_data')->getZipcodeimportId() ) {
            return Mage::helper('zipcodeimport')->__("Edit Zip Code '%s'", $this->htmlEscape(Mage::registry('zipcodeimport_data')->getZipCode()));
        } else {
            return Mage::helper('zipcodeimport')->__('Add Zip Code');
        }
    }
}