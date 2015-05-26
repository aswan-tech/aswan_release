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
