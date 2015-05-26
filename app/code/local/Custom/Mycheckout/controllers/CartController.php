<?php

require_once 'Mage/Checkout/controllers/CartController.php';
 
class Custom_Mycheckout_CartController extends Mage_Core_Controller_Front_Action
{

protected $_cookieCheckActions = array('add');

    /**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }

    /**
     * Get checkout session model instance
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get current active quote instance
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        return $this->_getCart()->getQuote();
    }

    /**
     * Set back redirect url to response
     *
     * @return Mage_Checkout_CartController
     */
    protected function _goBack()
    {
        $returnUrl = $this->getRequest()->getParam('return_url');
        if ($returnUrl) {

            if (!$this->_isUrlInternal($returnUrl)) {
                throw new Mage_Exception('External urls redirect to "' . $returnUrl . '" denied!');
            }

            $this->_getSession()->getMessages(true);
            $this->getResponse()->setRedirect($returnUrl);
        } elseif (!Mage::getStoreConfig('checkout/cart/redirect_to_cart')
            && !$this->getRequest()->getParam('in_cart')
            && $backUrl = $this->_getRefererUrl()
        ) {
            $this->getResponse()->setRedirect($backUrl);
        } else {
            if (($this->getRequest()->getActionName() == 'add') && !$this->getRequest()->getParam('in_cart')) {
                $this->_getSession()->setContinueShoppingUrl($this->_getRefererUrl());
            }
            $this->_redirect('checkout/cart');
        }
        return $this;
    }

    /**
     * Initialize product instance from request data
     *
     * @return Mage_Catalog_Model_Product || false
     */
    protected function _initProduct()
    {
        $productId = (int) $this->getRequest()->getParam('product');
        if ($productId) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);
            if ($product->getId()) {
                return $product;
            }
        }
        return false;
    }
	
	/**
     * Shopping cart display action
     */
    public function indexAction()
    {

/*
$skuArray = array('102023', '102024', '102025', '102026', '102027', '102028', '102029', '102030', '102032', '102033', '102035', '102036', '102037', '103959', '103960', '103961', '103962');
$quote = Mage::getModel('checkout/cart')->getQuote();
$couponCode = $this->_getQuote()->getCouponCode();
$oCoupon = Mage::getModel('salesrule/coupon')->load($couponCode, 'code');
foreach ($quote->getAllItems() as $item) {
    $cats = $item->getProduct()->getCategoryIds();
    foreach ($cats as $category_id) {
        if ($category_id == 234 || $category_id == 235) {
                $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
                $this->_getQuote()->setCouponCode('')
                ->collectTotals()
                ->save();
                break;
        }
    }
    if (in_array($item->getProduct()->getSku(), $skuArray) && strtolower($couponCode) != strtolower('ASPBH300') && $oCoupon->getRuleId() != 1751) {
#die('here');
        $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->_getQuote()->setCouponCode('')
                ->collectTotals()
                ->save();
    }
}
#die('=======================');
*/

		$cart = $this->_getCart();
		// clear coupon code from cart if giftcard and normal products are available. 
		$itemArr = Mage::helper('common')->getItemByType();
		if(in_array('giftcard', $itemArr)) {
			$quote = $cart->getQuote();
			$quote->setData('coupon_code','')->save();
			$cart->setQuote($quote)->save();
			$cart = $this->_getCart();			
		}
        
        if ($cart->getQuote()->getItemsCount()) {
            $cart->init();
            $cart->save();

            if (!$this->_getQuote()->validateMinimumAmount()) {
				$minimumAmount = Mage::getStoreConfig('sales/minimum_order/amount', $storeId);
				$minimumAmount = Mage::helper('common')->currency($minimumAmount, true, Mage::app()->getStore()->getCurrentCurrencyCode());
				
                //$minimumAmount = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())
                    //->toCurrency(Mage::getStoreConfig('sales/minimum_order/amount'));
				
				if(Mage::app()->getStore()->getBaseCurrencyCode() == Mage::app()->getStore()->getCurrentCurrencyCode()){
					$warning = Mage::getStoreConfig('sales/minimum_order/description')
								? Mage::getStoreConfig('sales/minimum_order/description')
								: Mage::helper('checkout')->__('Minimum order amount is %s', $minimumAmount);
				}else{
					$warning = Mage::helper('checkout')->__('Minimum order amount is %s', $minimumAmount);
				}
				
                $cart->getQuote()->setHasError(true)->addMessage($warning);
            }
        }

        // Compose array of messages to add
        $messages = array();
        foreach ($cart->getQuote()->getMessages() as $message) {
            if ($message) {
                // Escape HTML entities in quote message to prevent XSS
                $message->setCode(Mage::helper('core')->escapeHtml($message->getCode()));
                $messages[] = $message;
            }
        }
        $cart->getCheckoutSession()->addUniqueMessages($messages);

        /**
         * if customer enteres shopping cart we should mark quote
         * as modified bc he can has checkout page in another window.
         */
        $this->_getSession()->setCartWasUpdated(true);

        Varien_Profiler::start(__METHOD__ . 'cart_display');
        $this
            ->loadLayout()
            ->_initLayoutMessages('checkout/session')
            ->_initLayoutMessages('catalog/session')
            ->getLayout()->getBlock('head')->setTitle($this->__('Shopping Cart'));
        $this->renderLayout();
        Varien_Profiler::stop(__METHOD__ . 'cart_display');
    }
   /**
     * Add product to shopping cart action
     */
	public function getCustomSession()
    {
        return Mage::getSingleton('checkout/session');
    }
	
    public function addAction()
    {

        $cart   = $this->_getCart();
        $params = $this->getRequest()->getParams();
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
                $this->_goBack();
                return;
            }
						
            $cart->addProduct($product, $params);
			            
			if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }
			
			/* Customization for adding premium packaging */
			//commenting code as this fetaure is being disabled tempriorly
			/*
			if (isset($params['premium_package'])) {
				//Check premium packaging type (should be simple)
				Mage::getModel('packaging/packaging')->checkPremiumPackagingType($params['premium_package']);
				
				$cart->addProductsByPremiumId($params['premium_package']);
			}
			*/
			/* Customization for adding premium packaging */
			
            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            /**
             * @todo remove wishlist observer processAddToCart
             */
            Mage::dispatchEvent('checkout_cart_add_product_complete',
                array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );

            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()){
                    $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
                    $this->_getSession()->addSuccess($message);
                }
                $this->_goBack();
            }
        } catch (Mage_Core_Exception $e) {
            if ($this->_getSession()->getUseNotice(true)) {
                $this->_getSession()->addNotice(Mage::helper('core')->escapeHtml($e->getMessage()));
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->_getSession()->addError(Mage::helper('core')->escapeHtml($message));
                }
            }

            $url = $this->_getSession()->getRedirectUrl(true);
            if ($url) {
                $this->getResponse()->setRedirect($url);
            } else {
                $this->_redirectReferer(Mage::helper('checkout/cart')->getCartUrl());
            }
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
            Mage::logException($e);
            $this->_goBack();
        }
    }
	
	/**
     * Update shopping cart data action
     */
    public function updatePostAction()
    {
        $updateAction = (string)$this->getRequest()->getParam('update_cart_action');

        switch ($updateAction) {
            case 'empty_cart':
                $this->_emptyShoppingCart();
                break;
            case 'update_qty':
                $this->_updateShoppingCart();
                break;
            default:
                $this->_updateShoppingCart();
        }

        //$this->_goBack();
    }

    /**
     * Update customer's shopping cart
     */
    protected function _updateShoppingCart()
    {
        try {
            $cartData = $this->getRequest()->getPost();
			
			$cartData = $cartData['cart'];
		
            if (is_array($cartData)) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                foreach ($cartData as $index => $data) {
                    if (isset($data['qty'])) {
						if($data['qty'] ==""){
							$data['qty'] = 0;
						}
                        $cartData[$index]['qty'] = $filter->filter(trim($data['qty']));
                    }
                }
                $cart = $this->_getCart();
                if (! $cart->getCustomerSession()->getCustomer()->getId() && $cart->getQuote()->getCustomerId()) {
                    $cart->getQuote()->setCustomerId(null);
                }

                $cartData = $cart->suggestItemsQty($cartData);
                $cart->updateItems($cartData)
                    ->save();
                return true;    
            }
            $this->_getSession()->setCartWasUpdated(true);
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError(Mage::helper('core')->escapeHtml($e->getMessage()));
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot update shopping cart.'));
            Mage::logException($e);
        }
    }
	
	public function indexajaxAction(){
		
		$cart = $this->_getCart();
        if ($cart->getQuote()->getItemsCount()) {
            $cart->init();
            $cart->save();
			
			if (!$this->_getQuote()->validateMinimumAmount()) {
				$minimumAmount = Mage::getStoreConfig('sales/minimum_order/amount', $storeId);
				$minimumAmount = Mage::helper('common')->currency($minimumAmount, true, Mage::app()->getStore()->getCurrentCurrencyCode());
				
                //$minimumAmount = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())
                    //->toCurrency(Mage::getStoreConfig('sales/minimum_order/amount'));
				
				if(Mage::app()->getStore()->getBaseCurrencyCode() == Mage::app()->getStore()->getCurrentCurrencyCode()){
					$warning = Mage::getStoreConfig('sales/minimum_order/description')
								? Mage::getStoreConfig('sales/minimum_order/description')
								: Mage::helper('checkout')->__('Minimum order amount is %s', $minimumAmount);
				}else{
					$warning = Mage::helper('checkout')->__('Minimum order amount is %s', $minimumAmount);
				}
				
                $cart->getQuote()->setHasError(true)->addMessage($warning);
            }
        }

        // Compose array of messages to add
        $messages = array();
        foreach ($cart->getQuote()->getMessages() as $message) {
            if ($message) {
                // Escape HTML entities in quote message to prevent XSS
                $message->setCode(Mage::helper('core')->escapeHtml($message->getCode()));
                $messages[] = $message;
            }
        }
        $cart->getCheckoutSession()->addUniqueMessages($messages);

        /**
         * if customer enteres shopping cart we should mark quote
         * as modified bc he can has checkout page in another window.
         */
        $this->_getSession()->setCartWasUpdated(true);
		
		$this->loadLayout();
		
		$this->renderLayout();
	}
	
	/**
     * Initialize coupon
     */
    public function couponPostAction()
    {
        /**
         * No reason continue with empty shopping cart
         */
        if (!$this->_getCart()->getQuote()->getItemsCount()) {
            $this->_goBack();
            return;
        }

        $couponCode = (string) $this->getRequest()->getParam('coupon_code');
        if ($this->getRequest()->getParam('remove') == 1) {
            $couponCode = '';
        }
        $oldCouponCode = $this->_getQuote()->getCouponCode();

        if (!strlen($couponCode) && !strlen($oldCouponCode)) {
            $this->_goBack();
            return;
        }

		if (strlen($couponCode)) {


/*
$skuArray = array('102023', '102024', '102025', '102026', '102027', '102028', '102029', '102030', '102032', '102033', '102035', '102036', '102037', '103959', '103960', '103961', '103962');
$quote = Mage::getModel('checkout/cart')->getQuote();
$oCoupon = Mage::getModel('salesrule/coupon')->load($couponCode, 'code');
foreach ($quote->getAllItems() as $item) {
    $cats = $item->getProduct()->getCategoryIds();
    foreach ($cats as $category_id) {
        if ($category_id == 234 || $category_id == 235) {
            $this->_getSession()->addError($this->__('Coupon "%s" is not applicable on some of products in your cart.', Mage::helper('core')->htmlEscape($couponCode)));
            $this->_goBack();
              return;
        }
    }
    if (in_array($item->getProduct()->getSku(), $skuArray) && strtolower($couponCode) != strtolower('ASPBH300') && $oCoupon->getRuleId() != 1751) {
       $this->_getSession()->addError($this->__('Coupon "%s" is not applicable on beauty products so pls remove it from cart then proceed to checkou', Mage::helper('core')->htmlEscape($couponCode)));
       $this->_goBack();
         return;
    }
}
*/
			$coupon_object = Mage::getModel("salesrule/coupon")->loadByCode($couponCode);			
			$coupon_data = $coupon_object->getData();
			
			if(empty($coupon_data)){
				$this->_getSession()->addError(
                        $this->__('"%s" is not a valid coupon.', Mage::helper('core')->htmlEscape($couponCode))
                    );
				$this->_goBack();
				return;
			}			
			else{
				$current_date = date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time()));				
				$coupon_expiry = $coupon_object->getExpirationDate();
				
				if(isset($coupon_expiry)){
					if($coupon_expiry < $current_date){
						$this->_getSession()->addError(
							$this->__('Coupon "%s" has expired.', Mage::helper('core')->htmlEscape($couponCode))
						);					
						$this->_goBack();
						return;
					}
				}
			}
		}

        try {
            $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->_getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
                ->collectTotals()
                ->save();

            if (strlen($couponCode)) {
                if ($couponCode == $this->_getQuote()->getCouponCode()) {
                    $this->_getSession()->addSuccess(
                        $this->__('Coupon "%s" is successfully applied.', Mage::helper('core')->htmlEscape($couponCode))
                    );
                }
                else {
                    $this->_getSession()->addError(
                        $this->__('Coupon "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode))
                    );
                }
            } else {
                $this->_getSession()->addSuccess($this->__('Coupon has been removed from cart.'));
            }

        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Coupon code is invalid.'));
            Mage::logException($e);
        }

        $this->_goBack();
    }
    
    /*
     * removeitemfromcartAction() method is used to remove from cart. This is used for giftcard management.
     * @param Null
     * @return Null
     */ 
    public function removeitemfromcartAction() {
		
		$buyparams = (string) $this->getRequest()->getParam('buyparams');
		
		try {
			$session= Mage::getSingleton('checkout/session');
			$quote = $session->getQuote();
			
			$cart = Mage::getModel('checkout/cart');
			$cartItems = $cart->getItems();
			
			foreach ($cartItems as $item) {
				if($buyparams == 'giftcard' && ($item->getProductType() == 'configurable' || $item->getProductType() == 'simple') ) {
					$quote->removeItem($item->getId())->save();
				}
				else if($buyparams == 'normalproduct'  && $item->getProductType() == 'giftcard') {
					$quote->removeItem($item->getId())->save();
				}
			}
		}
		catch(Exception $e) {
			$this->_getSession()->addError($this->__('Error'));
            Mage::logException($e);
		}
		
		$this->renderLayout();
	}
	
	/*
	 * updateQtyAction() method is used to update qty from right area of checkout page
	 * @param null
	 * @return String
	 */
	 
	public function updateQtyAction()
    {
        if($this->getRequest()->isPost()) {
			$data = $this->_updateShoppingCart();
			if($data) {
				die("1");
			}
		}
		else {
			die("Invalid date posted");
		}
    }
    
    public function deleteShippingAddressAction() {
		if($this->getRequest()->isPost() && $this->getRequest()->getPost('address_id') > 0) {
			$address_id = (int)$this->getRequest()->getPost('address_id');
			$address = Mage::getModel('customer/address')->load($address_id);
			$address->delete();
			die("1");
		}
		else {
			die("-1");
		}
	}
	
	/*
	 * applycouponpostAction() method is used to apply coupon from order summary page
	 * @param Null
	 * @return String
	 */ 
	public function applycouponpostAction()
    {
        /**
         * No reason continue with empty shopping cart
         */
        if (!$this->_getCart()->getQuote()->getItemsCount()) {
            return false;
        }

        $couponCode = (string) $this->getRequest()->getParam('coupon_code');
        if(empty(trim($couponCode))) {
			echo 'Please enter valid coupon code.';
			return false;
		}
        
        if ($this->getRequest()->getParam('remove') == 1) {
            $couponCode = '';
        }
        $oldCouponCode = $this->_getQuote()->getCouponCode();

        if (!strlen($couponCode) && !strlen($oldCouponCode)) {
            return false;
        }
		
		if (strlen($couponCode)) {
			$coupon_object = Mage::getModel("salesrule/coupon")->loadByCode($couponCode);			
			$coupon_data = $coupon_object->getData();
			
			if(empty($coupon_data)){
				echo $this->__('%s is not a valid coupon.', Mage::helper('core')->htmlEscape($couponCode));
				return false;
			}			
			else{
				$current_date = date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time()));				
				$coupon_expiry = $coupon_object->getExpirationDate();
				
				if(isset($coupon_expiry) && ($coupon_expiry < $current_date) ){
					if($coupon_expiry < $current_date){
						echo $this->__('Coupon %s has expired.', Mage::helper('core')->htmlEscape($couponCode));				
						return false;
					}
				}
			}
		}

        try {
            
            $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->_getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
                ->collectTotals()
                ->save();

            if (strlen($couponCode)) {
                if ($couponCode == $this->_getQuote()->getCouponCode()) {
                    echo $this->__('Coupon "%s" is successfully applied.', Mage::helper('core')->htmlEscape($couponCode))."~1";
                }
                else {
                   echo $this->__('Coupon "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode));
                }
            } else {
                echo $this->__('Coupon has been removed from cart.~1');
            }

        } catch (Mage_Core_Exception $e) {
            echo $e->getMessage();
        } catch (Exception $e) {
            echo $this->__('Coupon code is invalid.');
            Mage::logException($e);
        }
        die();
    } 
}
