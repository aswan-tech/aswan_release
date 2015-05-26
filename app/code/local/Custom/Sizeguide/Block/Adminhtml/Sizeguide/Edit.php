<?php

class Custom_Sizeguide_Block_Adminhtml_Sizeguide_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'sizeguide';
        $this->_controller = 'adminhtml_sizeguide';
        
        $this->_updateButton('save', 'label', Mage::helper('sizeguide')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('sizeguide')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('sizeguide_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'sizeguide_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'sizeguide_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('sizeguide_data') && Mage::registry('sizeguide_data')->getId() ) {
            return Mage::helper('sizeguide')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('sizeguide_data')->getTitle()));
        } else {
            return Mage::helper('sizeguide')->__('Add Item');
        }
    }
}