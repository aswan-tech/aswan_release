<?php

class FCM_Productreports_Block_Adminhtml_Report_Renderer_Configurable extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render for Content
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
		$typeId =  $row->getData('type_id');
		
		$configSku = ""; 
		
		if ($typeId == "configurable") {
			$configSku = $row->getData($this->getColumn()->getIndex());
		} else if ($typeId == "simple") {
			$simpleProductId = $row->getData('entity_id');
		
			$objConfigProduct = Mage::getModel('catalog/product_type_configurable');
			$arrConfigProductIds = $objConfigProduct
										->getParentIdsByChild($simpleProductId);
										
			$configSkuArr = array();
			
			if (is_array($arrConfigProductIds)) {
				foreach($arrConfigProductIds as $sid) {
					$pr = Mage::getModel('catalog/product')->load($sid);
					$configSkuArr[] = $pr->getSku();
				}
			}
			
			if (count($configSkuArr) > 0) {
				$configSku = implode(", ", $configSkuArr);
			}
		}
		
		return $configSku;
    }

}