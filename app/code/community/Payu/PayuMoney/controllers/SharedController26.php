<?php 

class Payu_PayuMoney_SharedController extends Payu_PayuMoney_Controller_Abstract
{
   
    protected $_redirectBlockType = 'payumoney/shared_redirect';
    protected $_paymentInst = NULL;
	
	
	public function  successAction()
    {
        $response = $this->getRequest()->getPost();
		Mage::getModel('payumoney/shared')->getResponseOperation($response);
        $this->_redirect('money/onepage/success');
    }
	
	
	
	 public function failureAction()
    {
       $response=$_REQUEST;
		Mage::getModel('payumoney/shared')->getResponseOperation($response);
		$this->getMoney()->clear();
	      //$this->_redirect('money/onepage/failure');
		  
		$this->loadLayout();
        $this->renderLayout();
    }


    public function canceledAction()
    {
	    $arrParams = $this->getRequest()->getParams();
	
       
		Mage::getModel('payumoney/shared')->getResponseOperation($arrParams);
		
		$this->getMoney()->clear();
		$this->loadLayout();
        $this->renderLayout();
    }


   

    
}
    
    