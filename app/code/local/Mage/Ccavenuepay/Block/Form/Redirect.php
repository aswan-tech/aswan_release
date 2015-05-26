<?php 
/**
 Please Do not edit or add any code in this file without permission of bluezeal.in.
@Developed by bluezeal.in

Magento version 1.7.0.2                 CCAvenue Version 1.31
                              
Module Version. bz-1.0                 Module release: September 2012

*/

class Mage_Ccavenuepay_Block_Form_Redirect extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
		 
		 
        $ccavenuepay = Mage::getModel('ccavenuepay/method_ccavenuepay');

        $form = new Varien_Data_Form();
        $form->setAction($ccavenuepay->getCcavenuepayUrl())
            ->setId('ccavenuepay_standard_checkout')
            ->setName('ecom')
            ->setMethod('post')
		    ->setUseContainer(true);
        foreach ($ccavenuepay->getStandardCheckoutFormFields('redirect') as $field=>$value) {
           $form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value));
        }
		
	
        $html = '<html>
				<body style="text-align:center;">';
       $html.= $this->__('You will be redirected to Ccavenuepay in a few seconds.<br /><center>');
	   $html.='<img src="'.$this->getSkinUrl('ccavenue/ccavenue.gif').'" border="1" alt="Logo" width="185px" height="70px" /><br /><br />';
	   $html.= '<img src="'.$this->getSkinUrl('ccavenue/ajax-loader.gif').'" alt="ajax-loader" align="center" width="128px" height="15px" /><br /></center>';
	   $html.= $this->__('Copyright bluezeal.in');
       $html.= $form->toHtml();
       $html.= '<script type="text/javascript">
	   			  function formsubmit()
				  {
				  	document.ecom.submit();	
				  }
				  setTimeout("formsubmit()", 3000);
	            </script>';
	  
        $html.= '</body></html>';

        return $html; 
    }
}

