<?php

class FCM_Logger_Block_Adminhtml_Cron_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('cron_form', array('legend'=>Mage::helper('logger')->__('Item information')));
     
      $fieldset->addField('cron_id', 'label', array(
          'label'     => Mage::helper('logger')->__('ID'),
          'class'     => 'required-entry',
          'required'  => false,
          'name'      => 'cron_id',
      ));

      $fieldset->addField('cron_name', 'label', array(
          'label'     => Mage::helper('logger')->__('Cron Name'),
          'required'  => false,
          'name'      => 'cron_name',
	  ));
	  
	  $fieldset->addField('start_time', 'label', array(
          'label'     => Mage::helper('logger')->__('Start Time'),
          'required'  => false,
          'name'      => 'start_time',
	  ));
		
	$fieldset->addField('finish_time', 'label', array(
          'label'     => Mage::helper('logger')->__('Finished Time'),
          'required'  => false,
          'name'      => 'finish_time',
	  ));
      $fieldset->addField('status', 'label', array(
          'label'     => Mage::helper('logger')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 'Finished',
                  'label'     => Mage::helper('logger')->__('Finished'),
              ),

              array(
                  'value'     => 'Failed',
                  'label'     => Mage::helper('logger')->__('Failed'),
              ),
			  
			  array(
                  'value'     => 'Processing',
                  'label'     => Mage::helper('logger')->__('Processing'),
              ),
          ),
      ));
	  
	  $fieldset->addField('message', 'label', array(
          'label'     => Mage::helper('logger')->__('Message'),
          'required'  => false,
          'name'      => 'message',
      ));
     
     
      if ( Mage::getSingleton('adminhtml/session')->getLoggerData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getLoggerData());
          Mage::getSingleton('adminhtml/session')->setLoggerData(null);
      } elseif ( Mage::registry('cron_data') ) {
          $form->setValues(Mage::registry('cron_data')->getData());
      }
      return parent::_prepareForm();
  }
}