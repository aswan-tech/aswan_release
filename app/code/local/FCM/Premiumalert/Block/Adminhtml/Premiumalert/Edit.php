<?php

class FCM_Premiumalert_Block_Adminhtml_Premiumalert_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'premiumalert';
        $this->_controller = 'adminhtml_premiumalert';
        
        $this->_updateButton('save', 'label', Mage::helper('premiumalert')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('premiumalert')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('premiumalert_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'premiumalert_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'premiumalert_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('premiumalert_data') && Mage::registry('premiumalert_data')->getId() ) {
            return Mage::helper('premiumalert')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('premiumalert_data')->getTitle()));
        } else {
            return Mage::helper('premiumalert')->__('Add Item');
        }
    }
}