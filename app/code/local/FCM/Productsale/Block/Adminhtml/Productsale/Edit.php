<?php

class FCM_Productsale_Block_Adminhtml_Productsale_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'productsale';
        $this->_controller = 'adminhtml_productsale';
        
        $this->_updateButton('save', 'label', Mage::helper('productsale')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('productsale')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('productsale_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'productsale_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'productsale_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('productsale_data') && Mage::registry('productsale_data')->getId() ) {
            return Mage::helper('productsale')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('productsale_data')->getTitle()));
        } else {
            return Mage::helper('productsale')->__('Add Item');
        }
    }
}