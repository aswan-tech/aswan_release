<?php  
/*********************************************************
 ** Inventory master modules							  **
 ** author @ Ajesh Prakash(ajesh.prakash@hcl.com) **
*********************************************************/

class FCM_Inventory_Block_Adminhtml_System_Config_Form_Field_Button extends Mage_Adminhtml_Block_System_Config_Form_Field 
{
    
       protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)     
        {         
				$this->setElement($element);
				$url = $this->getUrl('inventory/adminhtml_inventory/import/run/1/cronname/product_inventory'); 
				$html = $this->getLayout()->createBlock('adminhtml/widget_button')
                          ->setType('button')
						  ->setClass('scalable')
						  ->setLabel(' Run Import ')
						  ->setOnClick("this.disabled = true;setLocation('$url');")
						  ->toHtml(); 
                return $html;  
	    } 
		
		
} 
?> 