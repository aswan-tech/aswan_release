<?php
class Product_Taggs_Helper_Data extends Mage_Core_Helper_Abstract {
	
	public function getMainCategories(){
			
		$categories = Mage::getModel('catalog/category')->getCollection()
		->addAttributeToSelect('*')
		->addAttributeToFilter('level', 2)//2 is actually the first level
		->addAttributeToFilter('is_active', 1);//if you want only active 
	
		$dataString = '';
	
		foreach ($categories as $data){
			
			$name = $data->getName();
			$dataString.='<option value='.$data->getId().'>'.ucfirst(strtolower($name)).'</option>';
		}
		return $dataString;
	}
}
