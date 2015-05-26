<?php

class FCM_Logger_Block_Adminhtml_Cron_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'logger';
        $this->_controller = 'adminhtml_cron';
        
        $this->_updateButton('save', 'label', Mage::helper('logger')->__('Save Item'));
        //$this->_updateButton('delete', 'label', Mage::helper('logger')->__('Delete Item'));
		$this->_removeButton('delete');
		$this->_removeButton('reset');
		$this->_removeButton('save');
		
        /*$this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);*/

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('cron_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'cron_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'cron_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('cron_data') && Mage::registry('cron_data')->getId() ) {
            return Mage::helper('logger')->__("View Detail");
        } else {
            return Mage::helper('logger')->__('Add Item');
        }
    }
}