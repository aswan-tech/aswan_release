<?php
/***********************************************************
 * Inventory master modules	Model
 * 
 *
 * @category    FCM
 * @package     FCM_Inventory
 * @author		Ajesh Prakash(ajesh.prakash@hcl.com) 
 * @company	HCL Technologies
 * @created Monday, June 6, 2012
 * @copyright	Four cross media
 **********************************************************/
class FCM_Inventory_Adminhtml_InventoryController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('inventory/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	
	/**
     Description: Upload inventory/price/image master csv files from FTP location to database by manual click
	 Input/OutPut: NA	 
     */
    public function importAction()
    {
	   $cronName = $this->getRequest()->getParam('cronname');		
       Mage::getModel('inventory/inventory')->importitemCsv($cronName); 
	   $this->_redirect('adminhtml/system_config/edit/section/inventory');
    }
}