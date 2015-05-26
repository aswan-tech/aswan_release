<?php
class Magestore_Departmentimages_Block_Adminhtml_Departmentimages extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_departmentimages';
    $this->_blockGroup = 'departmentimages';
    $this->_headerText = Mage::helper('departmentimages')->__('Manage Home Page Department Banners/E-Spots');
	
	// $category = Mage::getModel('catalog/category')->load(2)->getChildrenCategories();
	
	// $department = Mage::getModel('departmentimages/departmentimages')->getCollection();
	
	// if($department->count() < $category->count()) {
		$this->_addButtonLabel = Mage::helper('departmentimages')->__('Add Banner/Image');
	// }
	
    parent::__construct();
  }
}