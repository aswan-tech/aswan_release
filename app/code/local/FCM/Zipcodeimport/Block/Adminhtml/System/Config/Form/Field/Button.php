<?php  
class FCM_Zipcodeimport_Block_Adminhtml_System_Config_Form_Field_Button extends Mage_Adminhtml_Block_System_Config_Form_Field {
    
       protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)     
        {   
				$url = $this->getUrl('zipcodeimport/adminhtml_zipcodeimport/import/');
				
					$html = "<input type=\"button\" id=\"ImportZip\" name=\"ImportZip\" value=\"Run Now\" onclick=\"javascript:importZipCode(this);\" />";
					$html .= "<SCRIPT>
						function importZipCode(ele) {
							var url = '".$url."';
							window.open(url,'_blank');
						}
				</SCRIPT>";
				return $html;
				
	    } 
		
		
} 
?> 