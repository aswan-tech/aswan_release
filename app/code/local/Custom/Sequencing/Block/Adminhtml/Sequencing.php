<?php  

class Custom_Sequencing_Block_Adminhtml_Sequencing extends Mage_Adminhtml_Block_Template {
	protected $_model;
	protected $_size_actions;
	protected $_type_actions;
	protected $_cat_attr_array;
	protected $_gender_attr_array;
	public function _construct(){
		$this->_model = Mage::getModel('sequencing/sequencing');
		$this->_size_actions = $this->_model->getSizeActionArray();
		$this->_type_actions = $this->_model->getTypeActionArray();
		$this->_cat_attr_array = $this->_model->getCategoryAttributeArray();
		$this->_gender_attr_array = $this->_model->getGenderAttributeArray();
	}
	public function getAllTopLevelCategories(){
			$categories = Mage::getModel('catalog/category')->getCollection()
    			->addAttributeToSelect('*')
    			->addAttributeToFilter('level', 2)//2 is actually the first level
  				->addAttributeToFilter('is_active', 1);//if you want only active categories

  				return $categories;
	}
}