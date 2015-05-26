<?php

class FCM_Logger_Block_Adminhtml_Logger_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'logger';
        $this->_controller = 'adminhtml_logger';
        
        $this->_updateButton('save', 'label', Mage::helper('logger')->__('Save Item'));
        //$this->_updateButton('delete', 'label', Mage::helper('logger')->__('Delete Item'));
		$this->_removeButton('delete');
		$this->_removeButton('reset');
		$this->_removeButton('save');
		$this->_removeButton('saveandcontinue');
		
        /*$this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);*/

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('logger_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'logger_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'logger_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('logger_data') && Mage::registry('logger_data')->getId() ) {
            return Mage::helper('logger')->__("View Detail");
        } else {
            return Mage::helper('logger')->__('Add Item');
        }
    }
}