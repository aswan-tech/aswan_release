<?php  
    /*
		@Description: Create Run button for Partial(new and updated) customer Export
		@Date: 07-June-2012
		@Author: Dhananjay Kumar
	*/
class FCM_CustomerExport_Block_Adminhtml_System_Config_Form_Field_Partial extends Mage_Adminhtml_Block_System_Config_Form_Field {
    
       protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)     
        {         
				$this->setElement($element);
				$url = $this->getUrl('customerexport/adminhtml_index/partialexport/'); 
				$html = $this->getLayout()->createBlock('adminhtml/widget_button')
                          ->setType('button')
						  ->setClass('scalable')
						  ->setLabel(' Partial Export ')
						  ->setOnClick("setLocation('$url')")
						  ->toHtml(); 
                return $html;  
	    } 
		
		
} 
?> 