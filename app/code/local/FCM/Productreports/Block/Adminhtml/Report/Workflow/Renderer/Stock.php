<?php
class FCM_Productreports_Block_Adminhtml_Report_Workflow_Renderer_Stock extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render for Stock
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
		$pid =  $row->getData($this->getColumn()->getIndex());
		$this_prod = Mage::getModel('catalog/product')->load($pid);
		$type = $this_prod->getTypeId();
		
		$qty = Mage::getModel('cataloginventory/stock_item')->loadByProduct($this_prod)->getQty();
		$qty = number_format($qty, 2, '.', '');
		
		if($qty == 0){
			if($type == 'configurable')
				return '-';
			else
				return 'No';//'<span style="color:red;">No</span>';
		}else{
			return 'Yes';
		}

    }

}