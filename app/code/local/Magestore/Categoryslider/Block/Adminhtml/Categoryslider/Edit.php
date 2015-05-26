<?php

class Magestore_Categoryslider_Block_Adminhtml_Categoryslider_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'categoryslider';
        $this->_controller = 'adminhtml_categoryslider';
        
        $this->_updateButton('save', 'label', Mage::helper('categoryslider')->__('Save Banner'));
        $this->_updateButton('delete', 'label', Mage::helper('categoryslider')->__('Delete Banner'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('categoryslider_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'categoryslider_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'categoryslider_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('categoryslider_data') && Mage::registry('categoryslider_data')->getId() ) {
            return Mage::helper('categoryslider')->__("Edit Banner '%s'", $this->htmlEscape(Mage::registry('categoryslider_data')->getTitle()));
        } else {
            return Mage::helper('categoryslider')->__('Add Banner');
        }
    }
}