<?php  
/*********************************************************
 ** Item master modules							  **
 ** author @ Ajesh Prakash(ajesh.prakash@hcl.com) **
*********************************************************/

class FCM_Itemmaster_Block_Adminhtml_System_Config_Form_Field_Button extends Mage_Adminhtml_Block_System_Config_Form_Field 
{
    
       protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)     
        {         
				$this->setElement($element);
				$url = $this->getUrl('itemmaster/adminhtml_itemmaster/import/run/1/cronname/item_master'); 
				$html = $this->getLayout()->createBlock('adminhtml/widget_button')
                          ->setType('button')
						  ->setClass('scalable')
						  ->setLabel(' Run Import ')
						  ->setOnClick("this.disabled=true; setLocation('$url');")
						  ->toHtml(); 
				$html .= "<SCRIPT>
						function importitemmaster(ele) {
							//alert('under Construction!');
							//alert('".$url."');
							//return false;
		
							ele.disabled = true;
							ele.value='Running......';
							var url = '".$url."';
							window.open(url,'_blank');
						}
				</SCRIPT>";
                return $html;  
	    } 
		
		
} 
?> 