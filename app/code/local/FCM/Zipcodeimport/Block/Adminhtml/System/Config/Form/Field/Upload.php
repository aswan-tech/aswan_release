<?php  
class FCM_Zipcodeimport_Block_Adminhtml_System_Config_Form_Field_Upload extends Mage_Adminhtml_Block_System_Config_Form_Field {
    
       protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)     
        {   
				
					$html = "<input type=\"submit\" id=\"Upload\" name=\"Upload\" value=\"Upload\" onclick=\"javascript:uploadFile(this);\" />";
					$html .= "<SCRIPT>
						function uploadFile(ele) {
							configForm.submit();
							if($('zipcodeimport_general_bannerfile').hasClassName('validation-failed')){
								
							}else{
								ele.disabled = true;
								ele.value='Running......';
							}
						}
				</SCRIPT>";
				return $html;
				
	    } 
		
		
} 
?> 