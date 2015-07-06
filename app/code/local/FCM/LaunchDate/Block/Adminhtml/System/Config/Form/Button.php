<?php

class FCM_LaunchDate_Block_Adminhtml_System_Config_Form_Button extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $url = Mage::helper("adminhtml")->getUrl('launchdate/adminhtml_launchdate/product/');
        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setType('button')
                    ->setClass('scalable')
                    ->setLabel('Run Now !')
                    ->setOnClick("runCronFulfillmentScript()")
                    ->toHtml();
					
		$html .= "<script type='text/javascript'>
					function runCronFulfillmentScript(elem)
					{
						var url = '". $url ."';
						window.open(url,'_blank');	
					}
				</script>";
		
        return $html;
    }
}
?>	
