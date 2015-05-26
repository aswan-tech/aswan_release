<?php  
class RocketWeb_ProductVideo_Block_Adminhtml_System_Config_Form_Field_Upload extends Mage_Adminhtml_Block_System_Config_Form_Field {

   protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element){
		$url = $this->getUrl('productvideo/adminhtml_videos/import/');
		//$html = "<input type=\"button\" id=\"Uploadcsv\" name=\"Uploadcsv\" value=\"Upload Csv\" onclick=\"javascript:uploadFile(this);\" class=\"save\" />";
		$html = "<button id=\"Uploadcsv\" name=\"Uploadcsv\" style=\"\" onclick=\"javascript:uploadFile(this);\" type=\"button\" title=\"Import Product Videos\" ><span><span><span>Upload Csv</span></span></span></button>";
		
		$html .= "<SCRIPT type='text/javascript'>
						function uploadFile(ele) {
							//ele.disabled = true;
							//ele.value='Running......';
							
							//var url = '".$url."';
							//window.open(url,'_blank');
							
							configForm.submit();
						}
				</SCRIPT>";
		return $html;
	}
}
?> 