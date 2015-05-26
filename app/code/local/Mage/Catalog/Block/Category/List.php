<?php
class Mage_Catalog_Block_Category_List extends Mage_Core_Block_Template
{
	public function getSubcategories($catId=NULL)
	{
	   if(empty($catId))
	   {
			$layer = Mage::getSingleton('catalog/layer');
			$_category = $layer->getCurrentCategory();
			$currentId= $_category->getId();
			$subCat = '';
			//$subCat = Mage::getModel('catalog/category')->load($currentId)->getChildrenCategories();
			$subCat = Mage::getModel('catalog/category')->load($currentId)->getChildren(); 
			if (!empty($subCat)) {
				$subCat = explode(",", $subCat);
			}
			
			//return $subCat->getData();	
			return $subCat;
	   }
		else
		{
		  $subCats[]['entity_id'] = $catId;
		}
		 return $subCats;
	}
}
?>