<?php

class FCM_Inventory_Block_Adminhtml_Inventory_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'inventory';
        $this->_controller = 'adminhtml_inventory';
        
        $this->_updateButton('save', 'label', Mage::helper('inventory')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('inventory')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('inventory_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'inventory_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'inventory_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('inventory_data') && Mage::registry('inventory_data')->getId() ) {
            return Mage::helper('inventory')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('inventory_data')->getTitle()));
        } else {
            return Mage::helper('inventory')->__('Add Item');
        }
    }
}