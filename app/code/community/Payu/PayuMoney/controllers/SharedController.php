<?php 

class Payu_PayuMoney_SharedController extends Payu_PayuMoney_Controller_Abstract
{
   
    protected $_redirectBlockType = 'payumoney/shared_redirect';
    protected $_paymentInst = NULL;
	
	
	public function  successAction()
    {
        $response = $this->getRequest()->getPost();
		Mage::getModel('payumoney/shared')->getResponseOperation($response);
        $this->_redirect('checkout/onepage/success');
    }
	
	
	
	 public function failureAction()
    {
       
	   $arrParams = $this->getRequest()->getPost();
	   Mage::getModel('payumoney/shared')->getResponseOperation($arrParams);
           print_r("Mukesh");
          $this->getCheckout()->clear();
	   $this->_redirect('checkout/onepage/failure');
    }


    public function canceledAction()
    {
	    $arrParams = $this->getRequest()->getParams();
	
       
		Mage::getModel('payumoney/shared')->getResponseOperation($arrParams);
		$this->getCheckout()->clear();
		$this->loadLayout();
               $this->renderLayout();
    }


   

    
}
    
    