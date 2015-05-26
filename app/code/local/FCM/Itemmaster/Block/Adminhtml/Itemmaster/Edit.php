<?php

class FCM_Itemmaster_Block_Adminhtml_Itemmaster_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'itemmaster';
        $this->_controller = 'adminhtml_itemmaster';
        
        $this->_updateButton('save', 'label', Mage::helper('itemmaster')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('itemmaster')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('itemmaster_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'itemmaster_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'itemmaster_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('itemmaster_data') && Mage::registry('itemmaster_data')->getId() ) {
            return Mage::helper('itemmaster')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('itemmaster_data')->getTitle()));
        } else {
            return Mage::helper('itemmaster')->__('Add Item');
        }
    }
}