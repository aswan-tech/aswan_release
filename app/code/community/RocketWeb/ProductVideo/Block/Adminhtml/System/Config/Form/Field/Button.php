<?php  
class RocketWeb_ProductVideo_Block_Adminhtml_System_Config_Form_Field_Button extends Mage_Adminhtml_Block_System_Config_Form_Field {
   
   protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element){
		$url = $this->getUrl('productvideo/adminhtml_videos/import/');
		
		$html = "<button id=\"Importvideos\" name=\"Importvideos\" style=\"\" onclick=\"javascript:importVideos(this);\" type=\"button\" title=\"Import Product Videos\" ><span><span><span>Run Now</span></span></span></button>";
		
		$html .= "<SCRIPT>
						function importVideos(ele) {
							var url = '".$url."';
							window.open(url,'_blank');
						}
				</SCRIPT>";
		return $html;
	}
}
?> 