<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Ccavenuepay
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */



      
 
class Mage_Ccavenuepay_CcavenuepayController extends Mage_Core_Controller_Front_Action
{
    
    
    protected $_order;
	
	 
    
    public function getOrder()
    {
        if ($this->_order == null) {
        }
        return $this->_order;
    }

    protected function _expireAjax()
    {
        if (!Mage::getSingleton('checkout/session')->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
            exit;
        }
    }

    /**
     * Get singleton with Ccavenuepay strandard order transaction information
     *
     * @return Mage_Ccavenuepay_Model_Standard
     */
    public function getStandard()
    {
        return Mage::getSingleton('Ccavenuepay/standard');
    }

    /**
     * When a customer chooses Ccavenuepay on Checkout/Payment page
     *
     */
    public function redirectAction()
    {
	
		$session = Mage::getSingleton('checkout/session');
		$session->setCcavenuepayStandardQuoteId($session->getQuoteId());
		$order = Mage::getModel('sales/order');
		$order->load(Mage::getSingleton('checkout/session')->getLastOrderId());
		$order->addStatusHistoryComment("Customer was redirected to CCAvenue",false);
		$order->save();
		if(Mage::getStoreConfig('payment/ccavenuepay/integration_technique')=='iframe')
		{
		$this->loadLayout();
		$this->getLayout()->getBlock( 'head' )->setTitle( $this->__( 'CCAvenue Payment' ) );
		$this->renderLayout();
		}
		else
		{
			$this->getResponse()->setBody($this->getLayout()->createBlock('Ccavenuepay/form_redirect')->toHtml());
		}
		$session->unsQuoteId();

    }

    
    public function cancelAction()
    {
		/*
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getCcavenuepayStandardQuoteId(true));

		 
		$order_history_comment='';	
        
        if ($session->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
            if ($order->getId()) {
				 
				$order_history_comments = $this->getCheckout()->getCcavenuepayErrorMessage();
				foreach($order_history_comments as $order_history_comment)
				{
					if($order_history_comment !='') $order->addStatusHistoryComment($order_history_comment,true);
				}			
                $order->cancel()->save();
            }
        }

       
		Mage::getSingleton('checkout/session')->addError("CcavenuePay Payment has been cancelled and the transaction has been declined.");
		if($order_history_comment!='')	Mage::getSingleton('checkout/session')->addError($order_history_comment);
		$this->_redirect('checkout/cart');
		*/
        if ( Mage::getSingleton( 'checkout/session' )->getLastRealOrderId() ) {
		  $this->cancelOrder( Mage::helper( 'ccavenuepay' )->__( 'The payment was cancelled at CC Avenue.' ) );
		  Mage_Core_Controller_Varien_Action::_redirect( 'checkout/onepage/failure', array( '_secure' => true) );

        }
        else {
            Mage_Core_Controller_Varien_Action::_redirect( 'checkout/onepage/failure', array( '_secure' => true) );
        }
          
    }
	/**
	 * Function to cancel an order with a message
	 * 
	 * @param  string $cancelMessage
	 * @return void
	 */
	public function cancelOrder( $cancelMessage = '' ) {
		if ( $cancelMessage == '' )
			$cancelMessage = Mage::helper( 'ccavenuepay' )->__( 'The payment was cancelled due to a problem.' );

		if ( Mage::getSingleton( 'checkout/session' )->getLastRealOrderId() ) {
			$order = Mage::getModel( 'sales/order' )->loadByIncrementId( Mage::getSingleton( 'checkout/session' )->getLastRealOrderId() );
			if ( $order->getId() ) {
				// Flag the order as 'cancelled' and save it
				#$order->cancel()->setState( Mage_Sales_Model_Order::STATE_CANCELED, true, $cancelMessage )->save();
			     //$order->setState('canceled', 'canceled', $cancelMessage, FALSE);
                 //$order->save();

            }
		}
	}

    
    public function  successAction()
    {
		
        if (!$this->getRequest()->isPost()) {
        $this->cancelAction();
			return false;
        }

        $status = true;

		$response = $this->getRequest()->getPost();		 		
		if (empty($response))  {
            $status = false;
        }
		
		$encResponse = '';
		 
		$ccavenuepay = Mage::getModel('ccavenuepay/method_ccavenuepay');
		
		 
		$encryptionkey 	= Mage::getStoreConfig('payment/ccavenuepay/encryptionkey');
		if(isset($response["encResp"])){ $encResponse 	= $response["encResp"]; }
		
		$rcvdString		= $ccavenuepay->decrypt($encResponse,$encryptionkey);	
		$decryptValues	= explode('&', $rcvdString);
		$dataSize		= sizeof($decryptValues);
		
		 
		
		$Order_Id		= '';
		$tracking_id	= '';
		$order_status	= '';
		$response_array	= array();
		 
		for($i = 0; $i < count($decryptValues); $i++) 
		{
	  		$information	= explode('=',$decryptValues[$i]);
			if(count($information)==2)
			{
				$response_array[$information[0]] = $information[1];
			}
			  
		}
		 
		 
		if(isset($response_array['order_id']))		$Order_Id		= $response_array['order_id'];
		if(isset($response_array['tracking_id']))	$tracking_id	= $response_array['tracking_id'];
		if(isset($response_array['order_status']))	$order_status	= $response_array['order_status'];
		if(isset($response_array['currency']))	$currency = $response_array['currency'];
		if(isset($response_array['Amount']))	$payment_mode = $response_array['Amount'];
		
		$order_history_comments ='';
		$order_history_keys =array('tracking_id','failure_message','payment_mode','card_name','status_code','status_message','bank_ref_no');
		foreach($order_history_keys as $order_history_key)
		{
		 
			if((isset($response_array[$order_history_key]))  && trim($response_array[$order_history_key])!='')
			{
				if(trim($response_array[$order_history_key]) == 'null' ) continue;
				$order_history_comments .= $order_history_key." : ".$response_array[$order_history_key];
			}
		}
		
		$order_history_comments_array= array();   
		$order_history_comments_array[] = $order_history_comments;
		 
		
	 
		if($order_status == "Success")
		{
			 
			$order = Mage::getModel('sales/order');
			$order->loadByIncrementId($Order_Id); 
			 
			$f_passed_status = Mage::getStoreConfig('payment/ccavenuepay/payment_success_status');
			$message = Mage::helper('Ccavenuepay')->__('Your payment is authorized.');
			$order->addStatusHistoryComment($message,false);
			//$order->setState($f_passed_status, $f_passed_status, $message, true);
			 
			
			if($order_history_comments !='') $order->addStatusHistoryComment($order_history_comments,false);
			//$order->setState($f_passed_status, true);
			$payment_confirmation_mail = Mage::getStoreConfig('payment/ccavenuepay/payment_confirmation_mail');
			if($payment_confirmation_mail=="1")
			{	
				//$order->sendOrderUpdateEmail(true,'Your payment is authorized.');
				$order->sendNewOrderEmail();
			}
			
			$order->save();
			
			$session = Mage::getSingleton('checkout/session');
			$session->setQuoteId($session->getCcavenuepayStandardQuoteId(true));
			
			Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();

			$this->_redirect('checkout/onepage/success', array('_secure'=>true));
		}
		else 
		{
			$error_message =  " Order Cancel due to order status ".$order_status;
			$order_history_comments_array[] = 	$error_message; 			
			$this->getCheckout()->setCcavenuepayErrorMessage($order_history_comments_array ); 			
			$this->cancelAction();
			return false;
		}
    }
	public function errorAction()
    {
        $this->_redirect('checkout/onepage/');
    }
	public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }
}
