<?php  
class CommerceExtensions_Productcrosssellupsellimportexport_Block_Adminhtml_System_Config_Form_Field_Upload extends Mage_Adminhtml_Block_System_Config_Form_Field {
    
       protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)     
        { 
			$html = "<button type=\"submit\" id=\"Upload\" name=\"Upload\" value=\"Upload\" onclick=\"javascript:uploadFile(this);\" ><span><span><span> Import File </span></span></span></button>";
			$html .= "<SCRIPT>
				function uploadFile(ele) {
					configForm.submit();
				}
				</SCRIPT>";
			return $html;
		}		
}
?> 