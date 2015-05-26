<?php
/**
 * Magento Block to display run button for Order Feed Generation and export
 *
 * This block defines display of button.
 *
 * @category    FCM
 * @package     FCM_Fulfillment
 * @author	Pawan Prakash Gupta
 * @author_id	51405591
 * @company	HCL Technologies
 * @created Monday, June 4, 2012
 * @copyright	Four cross media
 */

/**
 * Adhoc run button for Order Feed Generation and export
 *
 * @category    FCM
 * @package     FCM_Fulfillment
 * @author      Pawan Prakash Gupta <51405591>
 */
 
class FCM_Fulfillment_Block_Adminhtml_System_Config_Form_Field_Otf extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $url = Mage::helper("adminhtml")->getUrl('fulfillment/adminhtml_fulfillment/exportotf/');
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
