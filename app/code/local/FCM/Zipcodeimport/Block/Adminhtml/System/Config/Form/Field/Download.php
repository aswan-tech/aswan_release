<?php  
class FCM_Zipcodeimport_Block_Adminhtml_System_Config_Form_Field_Download extends Mage_Adminhtml_Block_System_Config_Form_Field {
    
       protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)     
        {   
				$url = $this->getUrl('zipcodeimport/adminhtml_zipcodeimport/download/');
				
					$html = "<input type=\"button\" id=\"Download\" name=\"Download\" value=\"Download\" onclick=\"javascript:downloadFile(this);\" />";
					$html .= "<SCRIPT>
						function downloadFile(ele) {
							window.location = '".$url."';
						}
				</SCRIPT>";
				return $html;				
	    }
		
} 
?> 