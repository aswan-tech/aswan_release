<?php 
 
class FCM_Zipcodeimport_Block_Adminhtml_System_Config_Form_Field_Cron extends Mage_Adminhtml_Block_System_Config_Form_Field {
    
       protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)     
        {   
				$url = $this->getUrl('common/index/setPromotion');
				
				$url = str_replace('index.php/','',$url);
				
					$html = "<input type=\"button\" id=\"cronrun\" name=\"cronrun\" value=\"Run Cron Now\" onclick=\"javascript:runcron(this);\" />";
					$html .= "<SCRIPT>
						function runcron(ele) {
							ele.disabled = true;
							ele.value='Running......';
							var url = '".$url."';
							window.open(url,'_blank');
						}
				</SCRIPT>";
				return $html;
	    } 
} 