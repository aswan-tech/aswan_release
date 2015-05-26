<?php
/**
 * FCM Order Tracker Module 
 *
 * Module for tracking Customer Order
 *
 * @category    FCM
 * @package     FCM_Tracker
 * @author	Vikrant Kumar Mishra
 * @author_id	51402601
 * @company	HCL Technologies
 * @created Thursday, June 7, 2012
 */
class FCM_Tracker_Block_Adminhtml_Tracker extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_tracker';
    $this->_blockGroup = 'tracker';
    $this->_headerText = Mage::helper('tracker')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('tracker')->__('Add Item');
    parent::__construct();
  }
}