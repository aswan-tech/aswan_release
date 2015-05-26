<?php

class Jextn_Testimonials_Block_Adminhtml_Testimonials_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'testimonials';
        $this->_controller = 'adminhtml_testimonials';
        
        $this->_updateButton('save', 'label', Mage::helper('testimonials')->__('Save Testimonial'));
        $this->_updateButton('delete', 'label', Mage::helper('testimonials')->__('Delete Testimonial'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('testimonials_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'testimonials_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'testimonials_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('testimonials_data') && Mage::registry('testimonials_data')->getId() ) {
            return Mage::helper('testimonials')->__("Edit Testimonial for '%s'", $this->htmlEscape(Mage::registry('testimonials_data')->getName()));
        } else {
            return Mage::helper('testimonials')->__('Add Testimonial');
        }
    }
}