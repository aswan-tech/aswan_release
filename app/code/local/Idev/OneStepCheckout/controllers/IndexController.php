<?php
class Idev_OneStepCheckout_IndexController extends Mage_Core_Controller_Front_Action {

    public function getOnepage() {
        return Mage::getSingleton('checkout/type_onepage');
    }

    public function successAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function logincheckoutAction(){
        
        $session = Mage::getSingleton('customer/session');

        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');
            
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $session->login($login['username'], $login['password']);
                //    if ($session->getCustomer()->getIsJustConfirmed()) {
                //        $this->_welcomeCustomer($session->getCustomer(), true);
                //    }
                    Mage::getSingleton('checkout/cart')->save();
                    $this->_redirect('onestepcheckout/index');
                } catch (Mage_Core_Exception $e) {
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $value = Mage::helper('customer')->getEmailConfirmationUrl($login['username']);
                            $message = Mage::helper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);
                            break;
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            $message = $e->getMessage();
                            break;
                        default:
                            $message = $e->getMessage();
                    }
                    Mage::getSingleton('core/session')->addError($message);
                    $session->setUsername($login['username']);
                    $this->_redirect('onestepcheckout/index');
                } catch (Exception $e) {
                    // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
                }
            } else {
                $session->addError($this->__('Login and password are required.'));
            }
        }
    }

    public function indexAction() {
		
        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->_redirect('checkout/cart');
            return;
        }
        if (!$quote->validateMinimumAmount()) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message');
            Mage::getSingleton('checkout/session')->addError($error);
            $this->_redirect('checkout/cart');
            return;
        }  
        
        if( $this->getRequest()->isPost() ) {
			$itemArr = Mage::helper('common')->getItemByType();
			if(in_array('giftcard', $itemArr) && (in_array('simple', $itemArr) || in_array('configurable', $itemArr)) ) {
				$this->_redirect('checkout/cart/?c=f');
				return false;
			}
		}
        
        $this->loadLayout();

        if(Mage::helper('onestepcheckout')->isEnterprise() && Mage::helper('customer')->isLoggedIn()) {
            $customerBalanceBlock = $this->getLayout()->createBlock('enterprise_customerbalance/checkout_onepage_payment_additional', 'customerbalance', array('template'=>'onestepcheckout/customerbalance/payment/additional.phtml'));
            $customerBalanceBlockScripts = $this->getLayout()->createBlock('enterprise_customerbalance/checkout_onepage_payment_additional', 'customerbalance_scripts', array('template'=>'onestepcheckout/customerbalance/payment/scripts.phtml'));
            $this->getLayout()->getBlock('choose-payment-method')->append($customerBalanceBlock)->append($customerBalanceBlockScripts);
        }

        if(is_object(Mage::getConfig()->getNode('global/models/googleoptimizer')) && Mage::getStoreConfigFlag('google/optimizer/active')){
            $googleOptimizer = $this->getLayout()->createBlock('googleoptimizer/code_conversion', 'googleoptimizer.conversion.script', array('after'=>'-'))->setScriptType('conversion_script')->setPageType('checkout_onepage_success');
            $this->getLayout()->getBlock('before_body_end')->append($googleOptimizer);
        }

        $this->renderLayout();
    }
    
    public function continueGuestAction(){
		
		$connection = Mage::getModel('core/resource')->getConnection('core_read');
        $email_address = (string)$this->getRequest()->getPost('value');
        $customer = Mage::getModel('customer/customer')->setWebsiteId(1)->loadByEmail( $email_address );
		$sql = "SELECT email FROM email_locks where FIND_IN_SET ('".$email_address."', email)";
		$email = $connection->fetchRow($sql);
        $_quote = Mage::getSingleton('checkout/session')->getQuote();
        
        if(isset($email['email']) && $email['email']!=''){
			echo "1";
		}
        else if( $customer->getId() > 0 ) {
	       $_quote->assignCustomer($customer);
	       Mage::getSingleton('core/session')->setExisingCustomer($email_address);
        }else {
			Mage::getSingleton('core/session')->unsExisingCustomer();
            $_quote->setCustomerEmail($email_address)->save();
            $_quote->getBillingAddress()->setEmail($email_address)->save();
            $_quote->getShippingAddress()->setEmail($email_address)->save();
        }
        
    }
    
    public function validateShippingTabAction(){
        $_quote = Mage::getSingleton('checkout/session')->getQuote();
        $_phoneNumber = $_quote->getShippingAddress()->getTelephone();
		$_quote->setShippingPassed(true)->save();
		echo $_phoneNumber;
    }
}

