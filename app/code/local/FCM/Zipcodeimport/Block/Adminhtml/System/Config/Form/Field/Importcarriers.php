<?php  
class FCM_Zipcodeimport_Block_Adminhtml_System_Config_Form_Field_Importcarriers extends Mage_Adminhtml_Block_System_Config_Form_Field {
    
       protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)     
        {   
				$url = $this->getUrl('zipcodeimport/adminhtml_zipcodeimport/importcarriers/');
				
					$html = "<input type=\"button\" id=\"ImportCarriers\" name=\"ImportCarriers\" value=\"Import Carriers\" onclick=\"javascript:importCarriers(this);\" />";
					$html .= "<SCRIPT>
						function importCarriers(ele) {
							var url = '".$url."';
							window.open(url,'_blank');
						}
				</SCRIPT>";
				return $html;
				
	    }
} 
?> 