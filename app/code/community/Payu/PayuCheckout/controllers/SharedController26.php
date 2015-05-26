<?php 

class Payu_PayuCheckout_SharedController extends Payu_PayuCheckout_Controller_Abstract
{
   
    protected $_redirectBlockType = 'payucheckout/shared_redirect';
    protected $_paymentInst = NULL;
	
	
	public function  successAction()
    {
        $response = $this->getRequest()->getPost();
		Mage::getModel('payucheckout/shared')->getResponseOperation($response);
        $this->_redirect('checkout/onepage/success');
    }
	
	
	
	 public function failureAction()
    {
       $response=$_REQUEST;
		Mage::getModel('payucheckout/shared')->getResponseOperation($response);
		$this->getCheckout()->clear();
	      //$this->_redirect('checkout/onepage/failure');
		  
		$this->loadLayout();
        $this->renderLayout();
    }


    public function canceledAction()
    {
	    $arrParams = $this->getRequest()->getParams();
	
       
		Mage::getModel('payucheckout/shared')->getResponseOperation($arrParams);
		
		$this->getCheckout()->clear();
		$this->loadLayout();
        $this->renderLayout();
    }


   

    
}
    
    