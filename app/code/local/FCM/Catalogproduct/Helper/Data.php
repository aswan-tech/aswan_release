<?php
class FCM_Catalogproduct_Helper_Data extends Mage_Core_Helper_Abstract
{	
	public function getAttributeOptions($attributeCode)
	{
		$attributeModel = Mage::getSingleton('eav/config')
							->getAttribute('catalog_product', $attributeCode);
		
		$_options = array();
		
		if ($attributeModel->usesSource()){
			$allOptions = $attributeModel->getSource()->getAllOptions();
			
			foreach ($allOptions as $option) {
				$_options[$option['value']] = $option['label'];
			}
		}
	
		return $_options;
	}
}