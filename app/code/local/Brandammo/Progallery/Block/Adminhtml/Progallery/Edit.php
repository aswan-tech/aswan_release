<?php

class Brandammo_Progallery_Block_Adminhtml_Progallery_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'progallery';
        $this->_controller = 'adminhtml_progallery';
        
        $this->_updateButton('save', 'label', Mage::helper('progallery')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('progallery')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('progallery_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'progallery_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'progallery_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('progallery_data') && Mage::registry('progallery_data')->getId() ) {
            return Mage::helper('progallery')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('progallery_data')->getTitle()));
        } else {
            return Mage::helper('progallery')->__('Add Item');
        }
    }
}