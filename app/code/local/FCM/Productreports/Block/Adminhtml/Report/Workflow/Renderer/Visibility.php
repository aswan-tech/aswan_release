<?php
class FCM_Productreports_Block_Adminhtml_Report_Workflow_Renderer_Visibility extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render for Content
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
		$pid =  $row->getData($this->getColumn()->getIndex());
		$this_prod = Mage::getModel('catalog/product')->load($pid);

		if($this_prod->getVisibility() == 1){
			return 'No';//'<span style="color:red;">No</span>';
		}else{
			return 'Yes';
		}
    }

}