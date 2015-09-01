<?php

class Idev_OneStepCheckout_AjaxController extends Mage_Core_Controller_Front_Action {

    public function add_extra_productAction() {
        $helper = Mage::helper('onestepcheckout/extraproducts');
        $product_id = $this->getRequest()->getPost('product_id');
        $remove = $this->getRequest()->getPost('remove', false);

        if ($helper->isValidExtraProduct($product_id)) {

            if (!$remove) {
                /* Add product to cart if it doesn't exist */
                $product = Mage::getModel('catalog/product')->load($product_id);
                $cart = Mage::getSingleton('checkout/cart');
                $cart->addProduct($product);
                $cart->save();
            } else {
                $items = Mage::helper('checkout/cart')->getCart()->getItems();
                foreach ($items as $item) {
                    if ($item->getProduct()->getId() == $product_id) {
                        Mage::helper('checkout/cart')->getCart()->removeItem($item->getId())->save();
                    }
                }
            }
        }
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function indexAction() {
        $resource = Mage::getResourceModel('sales/order_collection');

        if (method_exists($resource, 'getEntity')) {
            echo 'Is using EAV';
        } else {
            echo 'Not using EAV';
        }

        die();

        var_dump($resource->getEntity());
        var_dump(get_class_methods($resource->getEntity()));
        var_dump($resource);

        die();

        var_dump(get_class_methods($resource));




        echo get_class($collection);
        echo '<br>';
        echo get_class($sales);

        var_dump(get_class_methods($collection));

        var_dump(get_class_methods($sales));
        var_dump($sales);

        die('<br><br>ajaxcontroller!');

        $this->loadLayout();
        $this->renderLayout();
    }

    protected function _isEmailRegistered($email) {
        $model = Mage::getModel('customer/customer');
        $model->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->loadByEmail($email);

        if ($model->getId() == NULL) {
            return false;
        }

        return true;
    }

    public function add_couponAction() {
        $quote = $this->_getOnepage()->getQuote();
        $couponCode = (string) $this->getRequest()->getParam('code');

        if ($this->getRequest()->getParam('remove') == 1) {
            $couponCode = '';
        }

        $response = array(
            'success' => false,
            'error' => false,
            'message' => false,
        );



        try {

            $quote->getShippingAddress()->setCollectShippingRates(true);
            $quote->setCouponCode(strlen($couponCode) ? $couponCode : '')
                    ->collectTotals()
                    ->save();

            if ($couponCode) {
                if ($couponCode == $quote->getCouponCode()) {
                    $response['success'] = true;
                    $response['message'] = $this->__('Coupon code "%s" was applied successfully.', Mage::helper('core')->htmlEscape($couponCode));
                } else {
                    $response['success'] = false;
                    $response['error'] = true;
                    $response['message'] = $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode));
                }
            } else {
                $response['success'] = true;
                $response['message'] = $this->__('Coupon code was canceled successfully.');
            }
        } catch (Mage_Core_Exception $e) {
            $response['success'] = false;
            $response['error'] = true;
            $response['message'] = $e->getMessage();
        } catch (Exception $e) {
            $response['success'] = false;
            $response['error'] = true;
            $response['message'] = $this->__('Can not apply coupon code.');
        }




        $html = $this->getLayout()
                        ->createBlock('checkout/onepage_shipping_method_available')
                        ->setTemplate('onestepcheckout/shipping_method.phtml')
                        ->toHtml();

        $response['shipping_method'] = $html;


        $html = $this->getLayout()
                        ->createBlock('checkout/onepage_payment_methods', 'choose-payment-method')
                        ->setTemplate('onestepcheckout/payment_method.phtml');

        if (Mage::helper('onestepcheckout')->isEnterprise() && Mage::helper('customer')->isLoggedIn()) {

            $customerBalanceBlock = $this->getLayout()->createBlock('enterprise_customerbalance/checkout_onepage_payment_additional', 'customerbalance', array('template' => 'onestepcheckout/customerbalance/payment/additional.phtml'));
            $customerBalanceBlockScripts = $this->getLayout()->createBlock('enterprise_customerbalance/checkout_onepage_payment_additional', 'customerbalance_scripts', array('template' => 'onestepcheckout/customerbalance/payment/scripts.phtml'));

            $this->getLayout()->getBlock('choose-payment-method')
                    ->append($customerBalanceBlock)
                    ->append($customerBalanceBlockScripts)
            ;
        
		if (Mage::helper('onestepcheckout')->hasEeRewards()) {
                $rewardPointsBlock = $this->getLayout()->createBlock('enterprise_reward/checkout_payment_additional', 'reward.points', array(
                    'template' => 'onestepcheckout/reward/payment/additional.phtml',
                    'before' => '-'
                ));
                $rewardPointsBlockScripts = $this->getLayout()->createBlock('enterprise_reward/checkout_payment_additional', 'reward.scripts', array(
                    'template' => 'onestepcheckout/reward/payment/scripts.phtml',
                    'after' => '-'
                ));
                $this->getLayout()
                    ->getBlock('choose-payment-method')
                    ->append($rewardPointsBlock)
                    ->append($rewardPointsBlockScripts);
            }
		
		}

        if (Mage::helper('onestepcheckout')->isEnterprise()) {
            $giftcardScripts = $this->getLayout()->createBlock('enterprise_giftcardaccount/checkout_onepage_payment_additional', 'giftcardaccount_scripts', array('template' => 'onestepcheckout/giftcardaccount/onepage/payment/scripts.phtml'));
            $html->append($giftcardScripts);
        }

        $response['payment_method'] = $html->toHtml();

        // Add updated totals HTML to the output
        $html = $this->getLayout()
                        ->createBlock('onestepcheckout/summary')
                        ->setTemplate('onestepcheckout/summary.phtml')
                        ->toHtml();

        $response['summary'] = $html;

        $this->getResponse()->setBody(Zend_Json::encode($response));
    }

    public function add_giftcardAction() {

        $response = array(
            'success' => false,
            'error' => true,
            'message' => $this->__('Cannot apply Gift Card, please try again later.'),
        );

        $code = $this->getRequest()->getParam('code', false);
        $remove = $this->getRequest()->getParam('remove', false);

        if (!empty($code) && empty($remove)) {
            try {
                Mage::getModel('enterprise_giftcardaccount/giftcardaccount')
                        ->loadByCode($code)
                        ->addToCart();

                $response['success'] = true;
                $response['error'] = false;
                $response['message'] = $this->__('Gift Card "%s" was added successfully.', Mage::helper('core')->htmlEscape($code));
            } catch (Mage_Core_Exception $e) {
                Mage::dispatchEvent('enterprise_giftcardaccount_add', array('status' => 'fail', 'code' => $code));

                $response['success'] = false;
                $response['error'] = true;
                $response['message'] = $e->getMessage();
            } catch (Exception $e) {
                Mage::getSingleton('checkout/session')->addException(
                        $e,
                        $this->__('Cannot apply Gift Card, please try again later.')
                );

                $response['success'] = false;
                $response['error'] = true;
                $response['message'] = $this->__('Cannot apply Gift Card, please try again later.');
            }
        }

        if (!empty($remove)) {
            $codes = $this->_getOnepage()->getQuote()->getGiftCards();
            if (!empty($codes)) {
                $codes = unserialize($codes);
            } else {
                $codes = array();
            }
            $response['message'] = $this->__('Cannot remove Gift Card, please try again later.');
            $messageCodes = array();
            foreach ($codes as $value) {
                try {
                    Mage::getModel('enterprise_giftcardaccount/giftcardaccount')
                            ->loadByCode($value['c'])
                            ->removeFromCart();
                    $messageCodes[] = $value['c'];
                    $response['success'] = true;
                    $response['error'] = false;
                    $response['message'] = $this->__('Gift Card "%s" was removed successfully.', Mage::helper('core')->htmlEscape(implode(', ', $messageCodes)));
                } catch (Mage_Core_Exception $e) {

                    $response['success'] = false;
                    $response['error'] = true;
                    $response['message'] = $e->getMessage();
                } catch (Exception $e) {
                    Mage::getSingleton('checkout/session')->addException(
                            $e,
                            $this->__('Cannot remove Gift Card, please try again later.')
                    );

                    $response['success'] = false;
                    $response['error'] = true;
                    $response['message'] = $this->__('Cannot remove Gift Card, please try again later.');
                }
            }
        }



        // Add updated totals HTML to the output
        $html = $this->getLayout()
                        ->createBlock('onestepcheckout/summary')
                        ->setTemplate('onestepcheckout/summary.phtml')
                        ->toHtml();

        $response['summary'] = $html;

        $html = $this->getLayout()
                        ->createBlock('checkout/onepage_shipping_method_available')
                        ->setTemplate('onestepcheckout/shipping_method.phtml')
                        ->toHtml();

        $response['shipping_method'] = $html;

        $html = $this->getLayout()
                        ->createBlock('checkout/onepage_payment_methods')
                        ->setTemplate('onestepcheckout/payment_method.phtml');

        if (Mage::helper('onestepcheckout')->isEnterprise()) {
            $giftcardScripts = $this->getLayout()->createBlock('enterprise_giftcardaccount/checkout_onepage_payment_additional', 'giftcardaccount_scripts', array('template' => 'onestepcheckout/giftcardaccount/onepage/payment/scripts.phtml'));
            $html->append($giftcardScripts);
        }

        $response['payment_method'] = $html->toHtml();

        $this->getResponse()->setBody(Zend_Json::encode($response));
    }

    public function check_emailAction() {
        $validator = new Zend_Validate_EmailAddress();
        $email = $this->getRequest()->getPost('email', false);

        $data = array('result' => 'invalid');


        if ($email && $email != '') {
            if (!$validator->isValid($email)) {
                
            } else {

                // Valid email, check for existance
                if ($this->_isEmailRegistered($email)) {
                    $data['result'] = 'exists';
                } else {
                    $data['result'] = 'clean';
                }
            }
        }

        $this->getResponse()->setBody(Zend_Json::encode($data));
    }

    public function forgotPasswordAction() {
        $email = $this->getRequest()->getPost('email', false);

        if (!Zend_Validate::is($email, 'EmailAddress')) {
            $result = array('success' => false);
        } else {

            $customer = Mage::getModel('customer/customer')
                            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                            ->loadByEmail($email);

            if ($customer->getId()) {
                try {
                    $newPassword = $customer->generatePassword();
                    $customer->changePassword($newPassword, false);
                    $customer->sendPasswordReminderEmail();
                    $result = array('success' => true);
                } catch (Exception $e) {
                    $result = array('success' => false, 'error' => $e->getMessage());
                }
            } else {
                $result = array('success' => false, 'error' => 'notfound');
            }
        }

        $this->getResponse()->setBody(Zend_Json::encode($result));
    }

    public function loginAction() {
        //$sessionId = session_id();
        $username = $this->getRequest()->getPost('onestepcheckout_username', false);
        $password = $this->getRequest()->getPost('onestepcheckout_password', false);
        $session = Mage::getSingleton('customer/session');

        $result = array('success' => false);

        if ($username && $password) {
            try {
                $session->login($username, $password);
            } catch (Exception $e) {
                $result['error'] = $e->getMessage();
            }
            if (!isset($result['error'])) {
                $result['success'] = true;
            }
        } else {
            $result['error'] = $this->__(
                            'Please enter a username and password.');
        }

        //session_id($sessionId);
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }

    public function save_billingAction() {
		
		$helper = Mage::helper('onestepcheckout/checkout');
		$resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $billing_data = $this->getRequest()->getPost('billing', array());
        $shipping_data = $this->getRequest()->getPost('shipping', array());

		$myarray = array();
		$_flagshipping	=	false;
		
		if($shipping_data['country_id'] == 'IN'){
			$_flagshipping	=	true;
		}
		$_flagbilling	=	false;
		if($billing_data['country_id'] == 'IN'){
			$_flagbilling	=	true;
		}
        $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);
        $shippingAddressId = $this->getRequest()->getPost('shipping_address_id', false);
        $billing_data = $helper->load_exclude_data($billing_data);
        $shipping_data = $helper->load_exclude_data($shipping_data);
        if( $shipping_data['address_id'] > 0 ) $shipping_data['save_in_address_book'] = 0;

		if($this->getRequest()->getPost('shipping_address_id', false)) {
			$shipping_data['save_in_address_book'] = 0;
		}
		
		if(isset($shipping_data['firstname'])){
			$name_pieces = explode(" ",trim($shipping_data['firstname']));
			
			$_parts = sizeof($name_pieces);
			
			if($_parts > 1){
				$shipping_data['firstname'] = $name_pieces[0];
				$shipping_data['lastname'] = $name_pieces[$_parts-1];
			}else{
				$shipping_data['lastname'] = $shipping_data['firstname'];
			}
			unset($name_pieces);
			unset($_parts);
		}
        if (Mage::helper('customer')->isLoggedIn()) {
            if (!empty($customerAddressId)) {
                $billingAddress = Mage::getModel('customer/address')->load($customerAddressId);
                if (is_object($billingAddress) && $billingAddress->getCustomerId() == Mage::helper('customer')->getCustomer()->getId()) {
                    $billing_data = array_merge($billing_data, $billingAddress->getData());
                }
            }
            if (!empty($shippingAddressId)) {
                $shippingAddress = Mage::getModel('customer/address')->load($shippingAddressId);
                if (is_object($shippingAddress) && $shippingAddress->getCustomerId() == Mage::helper('customer')->getCustomer()->getId()) {
				
                    $shipping_data = array_merge($shipping_data, $shippingAddress->getData());
					
					if(isset($shipping_data['address_id']) && $shipping_data['address_id'] == ''){
						$shipping_data['address_id'] = $this->_getOnepage()->getQuote()->getShippingAddress()->getId();
					}
                }
            }else{
					if(isset($shipping_data['address_id']) && $shipping_data['address_id'] == ''){
						$shipping_data['address_id'] = $this->_getOnepage()->getQuote()->getShippingAddress()->getId();
					}
			}
        }
		
        if (!empty($shipping_data['use_for_billing'])) {
			$billing_data = $shipping_data;
        }else{
			if(isset($billing_data['firstname'])){
				$name_pieces = explode(" ",trim($billing_data['firstname']));
			
				$_parts = sizeof($name_pieces);
			
				if($_parts > 1){
					$billing_data['firstname'] = $name_pieces[0];
					$billing_data['lastname'] = $name_pieces[$_parts-1];
				}else{
					$billing_data['lastname'] = $billing_data['firstname'];
				}
				unset($name_pieces);
				unset($_parts);
			}
		}

        // set customer tax/vat number for further usage
        if (!empty($billing_data['taxvat'])) {
            $this->_getOnepage()->getQuote()->setCustomerTaxvat($billing_data['taxvat']);
            $this->_getOnepage()->getQuote()->setTaxvat($billing_data['taxvat']);
            $this->_getOnepage()->getQuote()->getBillingAddress()->setTaxvat($billing_data['taxvat']);
        } else {
            $this->_getOnepage()->getQuote()->setCustomerTaxvat('');
            $this->_getOnepage()->getQuote()->setTaxvat('');
            $this->_getOnepage()->getQuote()->getBillingAddress()->setTaxvat('');
        }
		
		/* custom code to set city and state for shipping in DB */
		$_postcode_post = $shipping_data['postcode'];
		
		if($_postcode_post != "" && $_flagshipping){
			$shipping_zip_values = Mage::getResourceModel('zipcodeimport/zipcodeimport_collection')->addFieldToFilter('zip_code', array('like' => $_postcode_post));
		
			$shipping_zip_values_array = $shipping_zip_values->getData();
		
			$query_ship = 'SELECT region_id FROM ' . $resource->getTableName('directory_country_region') . ' WHERE default_name = "'.$shipping_zip_values_array[0]['state'].'" ';
		
			$result_ship = $readConnection->fetchRow($query_ship);
			
			$region_id = $result_ship['region_id'];
			
			$region = $shipping_zip_values_array[0]['state'];
			
			$shipping_data['region'] = $region;
			
			if(!empty($shipping_zip_values_array[0]['city'])){
				$shipping_data['city'] = $shipping_zip_values_array[0]['city'];
			}
			
			$shipping_data['region_id'] = $region_id;
			
			unset($_postcode_post);
			unset($shipping_zip_values);
			unset($shipping_zip_values_array);
			unset($result_ship);
			unset($region_id);
			unset($region);
		}
		/* custom code to set city and state for shipping in DB */
		
		$this->_getOnepage()->getQuote()->getShippingAddress()->addData($shipping_data)->implodeStreetAddress()->setCollectShippingRates(true);

        $paymentMethod = $this->getRequest()->getPost('payment_method', false);
        $selectedMethod = $this->_getOnepage()->getQuote()->getPayment()->getMethod();

        $store = $this->_getOnepage()->getQuote() ? $this->_getOnepage()->getQuote()->getStoreId() : null;
        $methods = $helper->getActiveStoreMethods($store, $this->_getOnepage()->getQuote());

        if ($paymentMethod && !empty($methods) && !in_array($paymentMethod, $methods)) {
            $paymentMethod = false;
        }

        if (!$paymentMethod && $selectedMethod && in_array($selectedMethod, $methods)) {
            $paymentMethod = $selectedMethod;
        }

        if ($this->_getOnepage()->getQuote()->isVirtual()) {
            $this->_getOnepage()->getQuote()->getBillingAddress()->setPaymentMethod(!empty($paymentMethod) ? $paymentMethod : null);
        } else {
            $this->_getOnepage()->getQuote()->getShippingAddress()->setPaymentMethod(!empty($paymentMethod) ? $paymentMethod : null);
        }

        try {
            if ($paymentMethod) {
                $this->_getOnepage()->getQuote()->getPayment()->getMethodInstance();
            }
        } catch (Exception $e) {

        }
        $shipping_result = $this->_getOnepage()->saveShipping($shipping_data, $shippingAddressId);
	    if ($shippingAddressId > 0 || $customerAddressId > 0 ) {
            // Do nothing.  We are not going the address thats already saved.
        } else {
            if (Mage::helper('customer')->isLoggedIn()) {
                $this->_getOnepage()->getQuote()->getBillingAddress()->setSaveInAddressBook(empty($billing_data['save_in_address_book']) ? 0 : 1);
                $this->_getOnepage()->getQuote()->getShippingAddress()->setSaveInAddressBook(empty($shipping_data['save_in_address_book']) ? 0 : 1);
            }
        }
		
		/* custom code to set city and state for billing in DB */
			if (empty($shipping_data['use_for_shipping'])) {
				$_postcode_post = $billing_data['postcode'];
				if($_postcode_post != "" && $_flagbilling){
					$billing_zip_values = Mage::getResourceModel('zipcodeimport/zipcodeimport_collection')->addFieldToFilter('zip_code', array('like' => $_postcode_post));
					$billing_zip_values_array = $billing_zip_values->getData();
					
					$query_bill = 'SELECT region_id FROM ' . $resource->getTableName('directory_country_region') . ' WHERE default_name = "'.$billing_zip_values_array[0]['state'].'" ';
					
					$result_bill = $readConnection->fetchRow($query_bill);
					
					$region_id = $result_bill['region_id'];
					
					$region = $billing_zip_values_array[0]['state'];
				
					$billing_data['region'] = $region;
									
					if(!empty($billing_zip_values_array[0]['city'])){
						$billing_data['city'] = $billing_zip_values_array[0]['city'];
					}
				
					$billing_data['region_id'] = $region_id;
					
					unset($_postcode_post);
					unset($billing_zip_values);
					unset($billing_zip_values_array);
					unset($query_bill);
					unset($result_bill);
					unset($region_id);
					unset($region);
				}
			}else{
				$_postcode_post = $shipping_data['postcode'];
				if($_postcode_post != "" && $_flagshipping){
					$shipping_zip_values = Mage::getResourceModel('zipcodeimport/zipcodeimport_collection')->addFieldToFilter('zip_code', array('like' => $_postcode_post));
					$shipping_zip_values_array = $shipping_zip_values->getData();
					
					$query_ship = 'SELECT region_id FROM ' . $resource->getTableName('directory_country_region') . ' WHERE default_name = "'.$shipping_zip_values_array[0]['state'].'" ';
					
					$result_ship = $readConnection->fetchRow($query_ship);
					
					$region_id = $result_ship['region_id'];
					
					$region = $shipping_zip_values_array[0]['state'];
				
					$shipping_data['region'] = $region;
					
					if(!empty($shipping_zip_values_array[0]['city'])){
						$shipping_data['city'] = $shipping_zip_values_array[0]['city'];
					}
				
					$shipping_data['region_id'] = $region_id;
					
					unset($_postcode_post);
					unset($shipping_zip_values);
					unset($shipping_zip_values_array);
					unset($query_ship);
					unset($result_ship);
					unset($region_id);
					unset($region);
				}
			}
		/* custom code to set city and state for billing in DB */
		
        if (empty($shipping_data['use_for_billing'])) {
			$this->_getOnepage()->getQuote()->getBillingAddress()->addData($billing_data)->implodeStreetAddress()->collectTotals();
            $result =$this->_getOnepage()->saveBilling($billing_data, $customerAddressId);
        } else {
			$this->_getOnepage()->getQuote()->getBillingAddress()->addData($shipping_data)->implodeStreetAddress()->collectTotals();
            $result = $this->_getOnepage()->saveBilling($shipping_data, $customerAddressId);
        }
        
        $shipping_method = $this->getRequest()->getPost('shipping_method', false);

        if (!empty($shipping_method)) {
            $helper->saveShippingMethod($shipping_method);
        }

        $this->_getOnepage()->getQuote()->setTotalsCollectedFlag(false)->collectTotals();

        $this->loadLayout(false);

        if (Mage::helper('onestepcheckout')->isEnterprise() && Mage::helper('customer')->isLoggedIn()) {

            $customerBalanceBlock = $this->getLayout()->createBlock('enterprise_customerbalance/checkout_onepage_payment_additional', 'customerbalance', array('template' => 'onestepcheckout/customerbalance/payment/additional.phtml'));
            $customerBalanceBlockScripts = $this->getLayout()->createBlock('enterprise_customerbalance/checkout_onepage_payment_additional', 'customerbalance_scripts', array('template' => 'onestepcheckout/customerbalance/payment/scripts.phtml'));

            $this->getLayout()->getBlock('choose-payment-method')
                    ->append($customerBalanceBlock)
                    ->append($customerBalanceBlockScripts)
            ;
			  
                $rewardPointsBlock = $this->getLayout()->createBlock('enterprise_reward/checkout_payment_additional', 'reward.points', array(
                    'template' => 'onestepcheckout/reward/payment/additional.phtml',
                    'before' => '-'
                ));
                $rewardPointsBlockScripts = $this->getLayout()->createBlock('enterprise_reward/checkout_payment_additional', 'reward.scripts', array(
                    'template' => 'onestepcheckout/reward/payment/scripts.phtml',
                    'after' => '-'
                ));
                $this->getLayout()
                    ->getBlock('choose-payment-method')
                    ->append($rewardPointsBlock)
                    ->append($rewardPointsBlockScripts);
          
			
        }

        if (Mage::helper('onestepcheckout')->isEnterprise()) {
            $giftcardScripts = $this->getLayout()->createBlock('enterprise_giftcardaccount/checkout_onepage_payment_additional', 'giftcardaccount_scripts', array('template' => 'onestepcheckout/giftcardaccount/onepage/payment/scripts.phtml'));
            $this->getLayout()->getBlock('choose-payment-method')
                    ->append($giftcardScripts);
        }

        $this->renderLayout();

		/* Custom code to set City and State on the basis of Postcode entered */
			$quote =  $this->_getOnepage()->getQuote();
			
			$_quote_billing = $quote->getBillingAddress();
			
			$_quote_shipping = $quote->getShippingAddress();

			$billing_zip_code = $_quote_billing->getPostcode();
		
			if($billing_zip_code != "" && $_flagbilling){
			$billing_zip_values = Mage::getResourceModel('zipcodeimport/zipcodeimport_collection')->addFieldToFilter('zip_code', array('like' => $billing_zip_code));
			
				$billing_zip_values_array = $billing_zip_values->getData();
			
				$query_bill = 'SELECT region_id FROM ' . $resource->getTableName('directory_country_region') . ' WHERE default_name = "'.$billing_zip_values_array[0]['state'].'" ';
			
				$result_bill = $readConnection->fetchRow($query_bill);
				
				if(isset($billing_zip_values_array[0]['state'])){
					$_state_bill = $billing_zip_values_array[0]['state'];
				}else{
					$_state_bill = '';
				}

				if(!empty($billing_zip_values_array[0]['city'])){
					$city_billing = $billing_zip_values_array[0]['city'];
				}else{
					$city_billing = $quote->getBillingAddress()->getCity();
				}
				$country_billing = $quote->getBillingAddress()->getCountryId();
				$myarray['billing'] = array(
						'postcode' => $billing_zip_code,
						'cod' => $billing_zip_values_array[0]['cod'],
						'city' => ($city_billing == '-') ? '' : $city_billing,
						'state' => $result_bill['region_id'],
						'statetext' => $_state_bill,
						'country' => $country_billing
					);
			}
		
			$shipping_zip_code = $_quote_shipping->getPostcode();
			
			if($shipping_zip_code != "" && $_flagshipping){
					$shipping_zip_values = Mage::getResourceModel('zipcodeimport/zipcodeimport_collection')->addFieldToFilter('zip_code', array('like' => $shipping_zip_code));

					$shipping_zip_values_array = $shipping_zip_values->getData();
			
					$query_ship = 'SELECT region_id FROM ' . $resource->getTableName('directory_country_region') . ' WHERE default_name = "'.$shipping_zip_values_array[0]['state'].'" ';
		  
					$result_ship = $readConnection->fetchRow($query_ship);
					
					if(isset($shipping_zip_values_array[0]['state'])){
						$_state_ship = $shipping_zip_values_array[0]['state'];
					}else{
						$_state_ship = '';
					}
					
					if(!empty($shipping_zip_values_array[0]['city'])){
						$city_shipping = $shipping_zip_values_array[0]['city'];
					}else{
						$city_shipping = $quote->getShippingAddress()->getCity();
					}
					$country_shipping = $quote->getShippingAddress()->getCountryId();
				$myarray['shipping'] = array(
						'postcode' => $shipping_zip_code,
						'cod' => $shipping_zip_values_array[0]['cod'],
						'city' => ($city_shipping == '-') ? '' : $city_shipping,
						'state' => $result_ship['region_id'],
						'statetext' => $_state_ship,
						'country' => $country_shipping
					);
			}
			
			if (!$quote->hasItems() || $quote->getHasError()) {
				$val = json_decode($this->getResponse()->getBody());
				$key = 'session_error';
				$val->$key = 'Your Quote has expired/Have Items which have some error, please add products to your shopping cart again';
				$this->getResponse()->setBody(Zend_Json::encode($val));
				return;
			}

			if (!empty($myarray) && isset($myarray['billing'])) {
				$val = json_decode($this->getResponse()->getBody());
				$key = 'billing';
				$val->$key = $myarray['billing'];
				$this->getResponse()->setBody(Zend_Json::encode($val));
			}

			if (!empty($myarray) && isset($myarray['shipping'])) {
				$val = json_decode($this->getResponse()->getBody());
				$key1 = 'shipping';
				$val->$key1 = $myarray['shipping'];
				$this->getResponse()->setBody(Zend_Json::encode($val));
			}
		/* Custom code ends */
    }

    public function paymentrefreshAction() {
        $payment_method = $this->getRequest()->getPost('payment_method');
        $helper = Mage::helper('onestepcheckout/checkout');
        if ($payment_method != '') {
            try {
                $payment = $this->getRequest()->getPost('payment', array());
                $payment['method'] = $payment_method;
                $this->_getOnepage()->getQuote()->getPayment()->setMethod($payment['method'])->getMethodInstance();
                $helper->savePayment($payment);
            } catch (Exception $e) {
                //die('Error: ' . $e->getMessage());
                // Silently fail for now
            }
        }

        $this->loadLayout(false);

        if (Mage::helper('onestepcheckout')->isEnterprise() && Mage::helper('customer')->isLoggedIn()) {

            $customerBalanceBlock = $this->getLayout()->createBlock('enterprise_customerbalance/checkout_onepage_payment_additional', 'customerbalance', array('template' => 'onestepcheckout/customerbalance/payment/additional.phtml'));
            $customerBalanceBlockScripts = $this->getLayout()->createBlock('enterprise_customerbalance/checkout_onepage_payment_additional', 'customerbalance_scripts', array('template' => 'onestepcheckout/customerbalance/payment/scripts.phtml'));

            $this->getLayout()->getBlock('choose-payment-method')
                    ->append($customerBalanceBlock)
                    ->append($customerBalanceBlockScripts)
            ;
        
		 $rewardPointsBlock = $this->getLayout()->createBlock('enterprise_reward/checkout_payment_additional', 'reward.points', array(
                    'template' => 'onestepcheckout/reward/payment/additional.phtml',
                    'before' => '-'
                ));
                $rewardPointsBlockScripts = $this->getLayout()->createBlock('enterprise_reward/checkout_payment_additional', 'reward.scripts', array(
                    'template' => 'onestepcheckout/reward/payment/scripts.phtml',
                    'after' => '-'
                ));
                $this->getLayout()
                    ->getBlock('choose-payment-method')
                    ->append($rewardPointsBlock)
                    ->append($rewardPointsBlockScripts);		
		}

        $this->renderLayout();

        $quote = Mage::getSingleton('checkout/type_onepage')->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $val = json_decode($this->getResponse()->getBody());
            $key = 'session_error';
            $val->$key = 'Your Quote has expired/Have Items which have some error, please add products to your shopping cart again';
            $this->getResponse()->setBody(Zend_Json::encode($val));
            return;
        }
    }

    public function set_methods_separateAction() {
        $helper = Mage::helper('onestepcheckout/checkout');

        $shipping_method = $this->getRequest()->getPost('shipping_method');
        $old_shipping_method = $this->_getOnepage()->getQuote()->getShippingAddress()->getShippingMethod();

        if ($shipping_method != '' && $shipping_method != $old_shipping_method) {
            // Use our helper instead
            $helper->saveShippingMethod($shipping_method);
        }

        $paymentMethod = $this->getRequest()->getPost('payment_method');
	
        $selectedMethod = $this->_getOnepage()->getQuote()->getPayment()->getMethod();

        $store = $this->_getOnepage()->getQuote() ? $this->_getOnepage()->getQuote()->getStoreId() : null;
        $methods = $helper->getActiveStoreMethods($store, $this->_getOnepage()->getQuote());

        if ($paymentMethod && !empty($methods) && !in_array($paymentMethod, $methods)) {
            $paymentMethod = false;
        }
		
		if($paymentMethod == ""){
			$paymentMethod = "cashondelivery";
		}
		
        if (!$paymentMethod && $selectedMethod && in_array($selectedMethod, $methods)) {
            $paymentMethod = $selectedMethod;
        }

        try {
            $payment = $this->getRequest()->getPost('payment', array());
            if (!empty($paymentMethod)) {
                $payment['method'] = $paymentMethod;
            }
            $helper->savePayment($payment);
            /*
             * Update additional data
             */ 
            
            $additional_data = array('mobikwik'=>$payment['mobikwik'],'Promo_Code'=>$payment['Promo_Code']);
            $helper->savePaymentAdditionalData(serialize($additional_data));
            
        } catch (Exception $e) {
            //die('Error: ' . $e->getMessage());
            // Silently fail for now
        }
        $this->_getOnepage()->getQuote()->collectTotals()->save();
        $this->loadLayout(false);

        if (Mage::helper('onestepcheckout')->isEnterprise() && Mage::helper('customer')->isLoggedIn()) {

            $customerBalanceBlock = $this->getLayout()->createBlock('enterprise_customerbalance/checkout_onepage_payment_additional', 'customerbalance', array('template' => 'onestepcheckout/customerbalance/payment/additional.phtml'));
            $customerBalanceBlockScripts = $this->getLayout()->createBlock('enterprise_customerbalance/checkout_onepage_payment_additional', 'customerbalance_scripts', array('template' => 'onestepcheckout/customerbalance/payment/scripts.phtml'));

            $this->getLayout()->getBlock('choose-payment-method')
                    ->append($customerBalanceBlock)
                    ->append($customerBalanceBlockScripts)
            ;
			 $rewardPointsBlock = $this->getLayout()->createBlock('enterprise_reward/checkout_payment_additional', 'reward.points', array(
                    'template' => 'onestepcheckout/reward/payment/additional.phtml',
                    'before' => '-'
                ));
                $rewardPointsBlockScripts = $this->getLayout()->createBlock('enterprise_reward/checkout_payment_additional', 'reward.scripts', array(
                    'template' => 'onestepcheckout/reward/payment/scripts.phtml',
                    'after' => '-'
                ));
                $this->getLayout()
                    ->getBlock('choose-payment-method')
                    ->append($rewardPointsBlock)
                    ->append($rewardPointsBlockScripts);
        }

        if (Mage::helper('onestepcheckout')->isEnterprise()) {
            $giftcardScripts = $this->getLayout()->createBlock('enterprise_giftcardaccount/checkout_onepage_payment_additional', 'giftcardaccount_scripts', array('template' => 'onestepcheckout/giftcardaccount/onepage/payment/scripts.phtml'));
            $this->getLayout()->getBlock('choose-payment-method')
                    ->append($giftcardScripts);
        }

        $this->renderLayout();

        $body = json_decode($this->getResponse()->getBody());

        $key1 = 'payment_method_val';
        $key2 = 'payment_type';
        $key3 = 'p_pid';

        $body->$key1 = $this->getRequest()->getPost('payment_method');
        $body->$key2 = $this->getRequest()->getPost('payment_type');
        $body->$key3 = $this->getRequest()->getPost('p_pid');

        $this->getResponse()->setBody(Zend_Json::encode($body));

        $quote = Mage::getSingleton('checkout/type_onepage')->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $val = json_decode($this->getResponse()->getBody());
            $key = 'session_error';
            $val->$key = 'Your Quote has expired/Have Items which have some error, please add products to your shopping cart again';
            $this->getResponse()->setBody(Zend_Json::encode($val));
            return;
        }
    }

    public function set_methodsAction() {
        $helper = Mage::helper('onestepcheckout/checkout');
        $shipping_method = $this->getRequest()->getPost('shipping_method');

        if ($shipping_method != '') {
            $helper->saveShippingMethod($shipping_method);
        }

        $payment_method = $this->getRequest()->getPost('payment_method');

        if ($payment_method != '') {
            try {
                $payment = $this->getRequest()->getPost('payment', array());
                $payment['method'] = $payment_method;
                $helper->savePayment($payment);
            } catch (Exception $e) {
                //die('Error: ' . $e->getMessage());
                // Silently fail for now
            }
        }

        $this->loadLayout(false);
        $this->renderLayout();
        $quote = Mage::getSingleton('checkout/type_onepage')->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $val = json_decode($this->getResponse()->getBody());
            $key = 'session_error';
            $val->$key = 'Your Quote has expired/Have Items which have some error, please add products to your shopping cart again';
            $this->getResponse()->setBody(Zend_Json::encode($val));
            return;
        }
    }

    public function set_payment_methodAction() {
        $payment_method = $this->getRequest()->getPost('payment_method');
        $payment = array('method' => $payment_method);
        $result = $this->_getOnepage()->savePayment($payment);

        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function set_shipping_methodAction() {
        $shipping_method = $this->getRequest()->getPost('shipping_method');
        $result = $this->_getOnepage()->saveShippingMethod($shipping_method);

        $this->loadLayout(false);
        $this->renderLayout();
    }

    protected function _getOnepage() {
        return Mage::getSingleton('checkout/type_onepage');
    }

    protected function _getSession() {
        return Mage::getSingleton('customer/session');
    }

    public function registerAction() {
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        if ($this->getRequest()->isPost()) {
            $errors = array();



            if (!$customer = Mage::registry('current_customer')) {
                $customer = Mage::getModel('customer/customer')->setId(null);
            }

            $lastOrderId = $this->_getOnepage()->getCheckout()->getLastOrderId();
            $order = Mage::getModel('sales/order')->load($lastOrderId);
            $billing = $order->getBillingAddress();

            $customer->setData('firstname', $billing->getFirstname());
            $customer->setData('lastname', $billing->getLastname());
            $customer->setData('email', $order->getCustomerEmail());


            foreach (Mage::getConfig()->getFieldset('customer_account') as $code => $node) {
                //echo $code . ' -> ' . $node . '<br/>';
                if ($node->is('create') && ($value = $this->getRequest()->getParam($code)) !== null) {
                    $customer->setData($code, $value);
                }
            }

            // print_r($customer->toArray());


            if ($this->getRequest()->getParam('is_subscribed', false)) {
                $customer->setIsSubscribed(1);
            }

            /**
             * Initialize customer group id
             */
            $customer->getGroupId();

            if ($this->getRequest()->getPost('create_address')) {
                $address = Mage::getModel('customer/address')
                                ->setData($this->getRequest()->getPost())
                                ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                                ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false))
                                ->setId(null);
                $customer->addAddress($address);

                $errors = $address->validate();
                if (!is_array($errors)) {
                    $errors = array();
                }
            }

            $result = array(
                'success' => false,
                'message' => false,
                'error' => false,
            );


            try {
                $validationCustomer = $customer->validate();
                if (is_array($validationCustomer)) {
                    $errors = array_merge($validationCustomer, $errors);
                }
                $validationResult = count($errors) == 0;

                //var_dump($validationResult);

                if (true === $validationResult) {

                    $customer->save();

                    $result['success'] = true;

                    if ($customer->isConfirmationRequired()) {

                        $customer->sendNewAccountEmail('confirmation', $this->_getSession()->getBeforeAuthUrl());
                        $this->_getSession()->addSuccess($this->__('Account confirmation is required. Please, check your e-mail for confirmation link. To resend confirmation email please <a href="%s">click here</a>.',
                                        Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail())
                        ));

                        $result['message'] = 'email_confirmation';
                    } else {
                        $this->_getSession()->setCustomerAsLoggedIn($customer);
                        $url = $this->_welcomeCustomer($customer);

                        $result['message'] = 'customer_logged_in';
                    }

                    $order->setCustomerId($customer->getId());
                    $order->setCustomerIsGuest(false);
                    $order->setCustomerGroupId($customer->getGroupId());
                    $order->save();
                } else {
                    $this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
                    if (is_array($errors)) {
                        $result['error'] = 'validation_failed';
                        $result['errors'] = $errors;
                    } else {
                        //$this->_getSession()->addError($this->__('Invalid customer data'));
                        $result['error'] = 'invalid_customer_data';
                    }
                }
            } catch (Mage_Core_Exception $e) {

                $result['error'] = $e->getMessage();

                //$this->_getSession()->addError($e->getMessage())
                //    ->setCustomerFormData($this->getRequest()->getPost());
            } catch (Exception $e) {

                $result['error'] = $e->getMessage();

                //$this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                //    ->addException($e, $this->__('Can\'t save customer'));
            }
        }

        $this->getResponse()->setBody(Zend_Json::encode($result));

        //
        //$result['error'] = 'redirect_to_create'
        ///die('About to redirect to create');
        //$this->_redirectError(Mage::getUrl('*/*/create', array('_secure'=>true)));
    }

    protected function _welcomeCustomer(Mage_Customer_Model_Customer $customer, $isJustConfirmed = false) {
        $this->_getSession()->addSuccess($this->__('Thank you for registering with %s', Mage::app()->getStore()->getName()));

        $customer->sendNewAccountEmail($isJustConfirmed ? 'confirmed' : 'registered');

        $successUrl = Mage::getUrl('*/*/index', array('_secure' => true));
        if ($this->_getSession()->getBeforeAuthUrl()) {
            $successUrl = $this->_getSession()->getBeforeAuthUrl(true);
        }
        return $successUrl;
    }
	
	/*
	 * ajaxlogincheckoutAction() method is used to Ajax Login
	 * @param Null
	 * @return String
	 */
	 
	 public function ajaxlogincheckoutAction() {
		
        $session = Mage::getSingleton('customer/session');

        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');
            
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $session->login($login['username'], $login['password']);
                    Mage::getSingleton('checkout/cart')->save();
                    die("1");
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
                    $session->setUsername($login['username']);
                    die($message);
                } catch (Exception $e) {
                    // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
                }
            } else {
               die('Login and password are required.');
            }
        }
        die();
	 }
	 
	 public function ajaxremoveitemcheckoutAction() {
		$id = (int) $this->getRequest()->getParam('id');
        if ($id) {
            try {
				$cart = Mage::getSingleton('checkout/cart');
                $cart->removeItem($id)->save();
                die("1");
            } catch (Exception $e) {
                die('Cannot remove the item.');
                Mage::logException($e);
            }
        }
        die();
	 } 
}

