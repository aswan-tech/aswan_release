<?php

class FCM_Productreports_Block_Adminhtml_Report_Renderer_Category extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render for Content
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
		$sku =  $row->getData($this->getColumn()->getIndex());
		
		$category = "";
		
		$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
		
		if (!empty($product)) {
			$cats = $product->getCategoryIds();
				
			$cnames = array();
			
			foreach ($cats as $category_id) {
				$_cat = Mage::getModel('catalog/category')->load($category_id) ;
				
				if ($_cat->getLevel() > 2) {
					$cnames[] = $_cat->getName();
				}
			}
			
			if (count($cnames) > 0) {
				$category = implode(",", $cnames);
			}
		}
		
		return $category;
    }

}