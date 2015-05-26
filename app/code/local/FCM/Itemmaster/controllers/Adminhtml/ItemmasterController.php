<?php
/***********************************************************
 * Item master modules 
 *
 *
 * @category    FCM
 * @package     FCM_Itemmaster
 * @author	Ajesh Prakash 
 * @company	HCL Technologies
 * @created Monday, June 6, 2012
 * @copyright	Four cross media
 **********************************************************/
class FCM_Itemmaster_Adminhtml_ItemmasterController extends Mage_Adminhtml_Controller_action
{	

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('itemmaster/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
	
	/**
     Description: Upload item master csv files from FTP location to database by manual click
	 Input/OutPut: NA	 
     */
    public function importAction()
    {	    
	   $cronName = $this->getRequest()->getParam('cronname');		
       Mage::getModel('itemmaster/itemmaster')->importitemCsv($cronName); 
	   $this->_redirect('adminhtml/system_config/edit/section/itemmaster');
    }
	
}