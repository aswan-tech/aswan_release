<?php

class FCM_Productreports_Block_Adminhtml_Report_Renderer_Simple extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
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
		
		$productSku = ""; 
		
		if ($typeId == "simple") {
			$productSku = $row->getData($this->getColumn()->getIndex());
		}
		
		return $productSku;
    }

}