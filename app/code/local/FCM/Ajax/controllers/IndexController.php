<?php

require_once 'Mage/Checkout/controllers/CartController.php';
class FCM_Ajax_IndexController extends Mage_Checkout_CartController
{
   /**
     * Add product to shopping cart action
     */
    public function addAction()
    {
        $cart   = $this->_getCart();
        $params = $this->getRequest()->getParams();
		if($params['isAjax'] == 1){
            $response = array();
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
                    $response['status'] = 'ERROR';
                    $response['message'] = $this->__('Unable to find Product ID');
                }
 
                $cart->addProduct($product, $params);
                if (!empty($related)) {
                    $cart->addProductsByIds(explode(',', $related));
                }
 
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
                        $message = $this->__('%s is added to your shopping cart.', Mage::helper('core')->htmlEscape($product->getName()));
                        $response['status'] = 'SUCCESS';
                        $response['message'] = $message;
//New Code Here
                        $this->loadLayout();
                        $toplink = $this->getLayout()->getBlock('top.links')->toHtml();
                        $sidebar = $this->getLayout()->getBlock('cart_sidebar')->toHtml();
                        $response['toplink'] = $toplink;
                        $response['sidebar'] = $sidebar;
                    }
                }
            } catch (Mage_Core_Exception $e) {
                $msg = "";
                if ($this->_getSession()->getUseNotice(true)) {
                    $msg = $e->getMessage();
                } else {
                    $messages = array_unique(explode("\n", $e->getMessage()));
                    foreach ($messages as $message) {
                        $msg .= $message.'<br/>';
                    }
                }
 
                $response['status'] = 'ERROR';
                $response['message'] = $msg;
            } catch (Exception $e) {
                $response['status'] = 'ERROR';
                $response['message'] = $this->__('Cannot add the item to shopping cart.');
                Mage::logException($e);
            }
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            //return;
        }else{
		  return parent::addAction();
		}        
    }
	
	/**
     *
     */
    public function loadquoteAction()
    {
        $params = $this->getRequest()->getParams();
        if(isset($params['id']))
        {
            //restore the quote
//            Mage::log($params['id']);

            $quote = Mage::getModel('sales/quote')->load($params['id']);
            if(!isset($params['token']) || (isset($params['token'])&&$params['token']!=$quote->getEbizmartsAbandonedcartToken())) {
                Mage::getSingleton('customer/session')->addNotice("Your token cart is incorrect");
                $this->_redirect('/');
            }
            else {
                $url = Mage::getStoreConfig(Ebizmarts_AbandonedCart_Model_Config::PAGE,$quote->getStoreId());
                $quote->setEbizmartsAbandonedcartFlag(1);
                $quote->save();
                if(!$quote->getCustomerId()) {
                    $this->_getSession()->setQuoteId($quote->getId());
                    $this->_redirect($url);
                }
                else {
                    if(Mage::getStoreConfig(Ebizmarts_AbandonedCart_Model_Config::AUTOLOGIN,$quote->getStoreId())) {
                        $customer = Mage::getModel('customer/customer')->load($quote->getCustomerId());
                        if($customer->getId())
                        {
                            Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
                        }
                        $this->_redirect($url);
                    }
                    else {
                        if(Mage::helper('customer')->isLoggedIn()) {
                            $this->_redirect($url);
                        }
                        else {
                            Mage::getSingleton('customer/session')->addNotice("Login to complete your order");
                            $this->_redirect('customer/account');
                        }
                    }
                }
            }
        }
//        $this->_redirect('checkout/cart');
    }
}