<?php

class Magestore_Departmentimages_Block_Adminhtml_Departmentimages_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'departmentimages';
        $this->_controller = 'adminhtml_departmentimages';
        
        $this->_updateButton('save', 'label', Mage::helper('departmentimages')->__('Save Banner'));
        $this->_updateButton('delete', 'label', Mage::helper('departmentimages')->__('Delete Banner'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('departmentimages_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'departmentimages_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'departmentimages_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('departmentimages_data') && Mage::registry('departmentimages_data')->getId() ) {
            return Mage::helper('departmentimages')->__("Edit Banner/Image '%s'", $this->htmlEscape(Mage::registry('departmentimages_data')->getTitle()));
        } else {
            return Mage::helper('departmentimages')->__('Add Banner/Image');
        }
    }
}