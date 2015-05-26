<?php
/**
 * FCM Logger Module 
 *
 * Module for tracking Log and Cron Detail
 *
 * @category    FCM
 * @package     FCM_Logger
 * @author	Vikrant Kumar Mishra
 * @author_id	51402601
 * @company	HCL Technologies
 * @created Thursday, June 7, 2012
 */
class FCM_Logger_Block_Adminhtml_Cron extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_cron';
    $this->_blockGroup = 'logger';
    $this->_headerText = Mage::helper('logger')->__('FCM Cron Master');
    $this->_addButtonLabel = Mage::helper('logger')->__('Add Item');
    parent::__construct();
	$this->removeButton('add');
	
  }
}