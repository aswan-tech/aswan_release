<?php

class FCM_Productreports_Block_Adminhtml_Report_Workflow_Renderer_Content extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render for Content
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
		$count = 0;
		
		$pid =  $row->getData($this->getColumn()->getIndex());
		$this_prod = Mage::getModel('catalog/product')->load($pid);
		
		//Check for product' "Description"
		if($this_prod->getDescription() != ''){
			$count++;
		}
		
		//Check for product' "Short Description"
		if($this_prod->getShortDescription() != ''){
			$count++;
		}
		
		//Check for product' "As Styling Slip" description
		if($this_prod->getAsStylingTip() != ''){
			$count++;
		}
		
		//Check for product' "Ablout Lecom Collection" description
		if($this_prod->getAboutLecomCollection() != ''){
			$count++;
		}
		
		//Check for product' "Delivery" description
		if($this_prod->getDelivery() != ''){
			$count++;
		}
		
		//Check for product' "Info & Care" description
		if($this_prod->getInfoCare() != ''){
			$count++;
		}
		
		//Check for product' "Return" description
		if($this_prod->getReturns() != ''){
			$count++;
		}
		
		if($count < 7){
			return 'No';//'<span style="color:red;">No</span>';
		}else{
			return 'Yes';
		}
		
        //return parent::render($row);
    }

}