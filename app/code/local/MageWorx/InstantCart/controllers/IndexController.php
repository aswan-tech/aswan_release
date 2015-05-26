<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   MageWorx
 * @package    MageWorx_InstantCart
 * @copyright  Copyright (c) 2012 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Instant Cart extension
 *
 * @category   MageWorx
 * @package    MageWorx_InstantCart
 * @author     MageWorx Dev Team
 */

require_once('Mage/Checkout/controllers/CartController.php');
class MageWorx_InstantCart_IndexController extends Mage_Checkout_CartController
{
    public function preDispatch() {
        $checkCookie = in_array($this->getRequest()->getActionName(), $this->_cookieCheckActions);
        $checkCookie = $checkCookie && !$this->getRequest()->getParam('nocookie', false);
        $cookies = Mage::getSingleton('core/cookie')->get();
        if ($checkCookie && empty($cookies)) {
            Mage::getSingleton('core/session', array('name' => $this->_sessionNamespace))->start();
            $this->getResponse()->setRedirect($this->getRequest()->getRequestUri().'?cookies')->sendResponse();
            exit;
        }

        parent::preDispatch();

        /*if (!$this->getRequest()->isXmlHttpRequest()) {
            $cartUrl = str_replace('/icart/', '/cart/', $this->getRequest()->getRequestUri());
            $this->getResponse()->setRedirect($cartUrl)->sendResponse();
            exit;
        }*/
    }

    public function addAction() {
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
            Mage::register('product', $product);
			
			if($product){
            
				if ('POST' != $this->getRequest()->getMethod()) {
					Mage::register('current_product', $product);
					if ($product->isGrouped() || $product->isConfigurable() || $product->getTypeInstance(true)->hasRequiredOptions($product) || (Mage::helper('icart')->isExtensionEnabled('MageWorx_CustomPrice') && Mage::helper('customprice')->isCustomPriceAllowed($product))){
						
						$update = $this->getLayout()->getUpdate();
						$this->addActionLayoutHandles();

						$update->addHandle('PRODUCT_TYPE_'.$product->getTypeId());
						$update->addHandle('PRODUCT_'.$product->getId());

						if ($product->getPageLayout()) {
							$this->getLayout()->helper('page/layout')
								->applyHandle($product->getPageLayout());
						}

						$this->loadLayoutUpdates();
						$update->addUpdate($product->getCustomLayoutUpdate());

						$this->generateLayoutXml()->generateLayoutBlocks();
											
						if ($product->getPageLayout()) {                        
							$this->getLayout()->helper('page/layout')
								->applyTemplate($product->getPageLayout());
						}
						//echo $this->getLayout()->getOutput();
						$this->renderLayout();
						return;
						exit;
					}
				}            
            }
            /**
             * Check product availability
             */
            if (!$product) {
                $this->_getSession()->addError(Mage::helper('checkout')->__('Product is not available.'));
                $this->_forward('added');
            }
            
            Mage::dispatchEvent('checkout_icart_add_before', array('controller_action' => $this));
                        
            $cart->addProduct($product, $params);                        
            
            $related = $this->getRequest()->getParam('related_product');
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }
			
			/* Customization for adding premium packaging */
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
                    $message = Mage::helper('checkout')->__('%s was added to your shopping cart.', Mage::helper('core')->htmlEscape($product->getName()));
                    $this->_getSession()->addSuccess($message);
                }else{
					$message = Mage::helper('checkout')->__('%s was added to your shopping cart, but cart has some errors, please resolve them first.', Mage::helper('core')->htmlEscape($product->getName()));
                    $this->_getSession()->addError($message);
				}
            }
        }
        catch (Mage_Core_Exception $e) {
            if ($this->_getSession()->getUseNotice(true)) {
                $this->_getSession()->addNotice($e->getMessage());
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->_getSession()->addError($message);
                }
            }
            $this->getRequest()->setParam('error', 1);
        }
        catch (Exception $e) {
            $this->_getSession()->addException($e, Mage::helper('checkout')->__('Cannot add the item to shopping cart.'));
            $this->getRequest()->setParam('error', 1);
        }
        $this->_forward('added');
    }
    
    public function editAction()
    {                
        
        $id = (int) $this->getRequest()->getParam('id');
        $quoteItem = null;
        $cart = $this->_getCart();                
        
        if ('POST' != $this->getRequest()->getMethod()){
            if ($id) {
                $quoteItem = $cart->getQuote()->getItemById($id);
            }

            if (!$quoteItem) {
                $this->_getSession()->addError(Mage::helper('checkout')->__('Quote item is not found.'));
                $this->_goBack();
                return;
            }

            try {
                $params = new Varien_Object();
                $params->setCategoryId(false);
                $params->setConfigureMode(true);
                $params->setBuyRequest($quoteItem->getBuyRequest());

                Mage::helper('icart/catalog_product_view')->prepareAndRender($quoteItem->getProduct()->getId(), $this, $params);                        


            } catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('checkout')->__('Cannot configure product.'));
                Mage::logException($e);
                $this->_goBack();
                return;
            }
            return;
        }           
                        
        $params = $this->getRequest()->getParams();

        if (!isset($params['options'])) {
            $params['options'] = array();
        }
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }
            
            
            $quoteItem = $cart->getQuote()->getItemById($id);
            if (!$quoteItem) Mage::throwException(Mage::helper('checkout')->__('Quote item is not found.'));
            
            if (isset($params['qty']) && $params['qty']==0) {
                $isRemoveItemFlag = true;
                $cart->removeItem($id);
            } else {
                $isRemoveItemFlag = false;

                $item = $cart->updateItem($id, new Varien_Object($params));

                if (is_string($item)) {
                    Mage::throwException($item);                
                }
                if ($item->getHasError()) {
                    Mage::throwException($item->getMessage());
                }

                $related = $this->getRequest()->getParam('related_product');
                if (!empty($related)) {
                    $cart->addProductsByIds(explode(',', $related));
                }                
                
            }    

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            Mage::dispatchEvent('checkout_cart_update_item_complete',
                array('item' => $item, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );
            if (!$this->_getSession()->getNoCartRedirect(true)) {                
                    
                if (!$cart->getQuote()->getHasError()) {
                    if ($isRemoveItemFlag) {
                        $message = Mage::helper('icart')->__('%s was deleted form your shopping cart.', Mage::helper('core')->htmlEscape($quoteItem->getName()));
                    } else {
                        $message = Mage::helper('checkout')->__('%s was updated in your shopping cart.', Mage::helper('core')->htmlEscape($item->getProduct()->getName()));
                    }
                    $this->_getSession()->addSuccess($message);
                }
                //$this->_goBack();
            }
        } catch (Mage_Core_Exception $e) {            
            if ($this->_getSession()->getUseNotice(true)) {
                $this->_getSession()->addNotice($e->getMessage());
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->_getSession()->addError($message);
                }
            }                        
            $this->getRequest()->setParam('error', 1);

        } catch (Exception $e) {
            $this->_getSession()->addException($e, Mage::helper('checkout')->__('Cannot update the item.'));
            Mage::logException($e);
            $this->getRequest()->setParam('error', 1);
        }        
        
        $this->_forward('added');
    }
    
    protected function _getWishlist()
    {
        try {
            $wishlist = Mage::getModel('wishlist/wishlist')
                ->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer(), true);
            Mage::register('wishlist', $wishlist);
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('wishlist/session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('wishlist/session')->addException($e,
                Mage::helper('wishlist')->__('Cannot create wishlist.')
            );
            return false;
        }
        return $wishlist;
    }
    
    public function addToWishlistAction()
    {                       
        
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {                
            $this->getLayout()->getUpdate()->addHandle('icart_index_authorization');
            $this->loadLayoutUpdates()->_initLayoutMessages('checkout/session');
            $this->generateLayoutXml()->generateLayoutBlocks();
            $this->renderLayout();                 
            $this->setFlag('', 'no-dispatch', true);
            
            Mage::getSingleton('customer/session')->setBeforeAuthUrl($this->_getRefererUrl());            
            return false;
        }
        
        $session = $this->_getSession();
        
        $productId = intval($this->getRequest()->getParam('product', false));
        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            $session->addError($this->__('Cannot specify product.'));
            $this->_forward('added');
            return false;
        }        
        
        
        try {
            $wishlist = $this->_getWishlist();        
            $buyRequest = new Varien_Object($this->getRequest()->getParams());

            $result = $wishlist->addNewItem($productId, $buyRequest);
            if (is_string($result)) {
                Mage::throwException($result);
            }
            $wishlist->save();            
            
            $session->addSuccess($this->__('%1$s was successfully added to your wishlist.', Mage::helper('core')->htmlEscape($product->getName())));            

        } catch (Mage_Core_Exception $e) {
            if ($session->getUseNotice(true)) {
                $session->addNotice($e->getMessage());
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $session->addError($message);
                }
            }
        } catch (Exception $e) {
            $session->addException($e, $this->__('Cannot add the item to wishlist.'));
        }
        
        $this->_forward('added');
    }
    
    
    public function addToCompareAction()
    {                                       
        
        $session = $this->_getSession();
        
        $productId = intval($this->getRequest()->getParam('product', false));
        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            $session->addError($this->__('Cannot specify product.'));
            $this->_forward('added');
            return false;
        }        
        
        try {            
            Mage::getSingleton('catalog/product_compare_list')->addProduct($product);                                    
            $session->addSuccess($this->__('The product %s has been added to comparison list.', Mage::helper('core')->htmlEscape($product->getName())));
            Mage::helper('catalog/product_compare')->calculate();
        } catch (Mage_Core_Exception $e) {
            if ($session->getUseNotice(true)) {
                $session->addNotice($e->getMessage());
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $session->addError($message);
                }
            }
        } catch (Exception $e) {
            $session->addException($e, $this->__('Cannot add the item to comparison list.'));
        }
        
        $this->_forward('added');
    }
    
    

    public function addedAction()
    {                
        $isInputFile = intval($this->getRequest()->getParam('is_input_file', 0));        
        if ($isInputFile) {
            $this->getResponse()->setBody('<script type="text/javascript">window.location="'.$this->getRequest()->getParam('referer_url', '').'"</script>');            
            return ;
        }
        
        $this->getLayout()->getUpdate()->addHandle('checkout_icart_added');
        $this->loadLayoutUpdates()->_initLayoutMessages('checkout/session');
        $this->generateLayoutXml()->generateLayoutBlocks();
        $this->renderLayout();
    }

    public function deleteAction()
    {
        $id = (int) $this->getRequest()->getParam('id');
        if ($id) {
            try {                                
                $this->_getCart()->removeItem($id)->save();
                
                $quote = $this->_getSession()->getQuote()->setHasError(false);
                $items = $quote->getAllVisibleItems();
                foreach ($items as $item) {
                    $item->checkData();
                }
                //$quote->setTotalsCollectedFlag(false)->collectTotals();
                
            } catch (Exception $e) {
                echo $this->__('Cannot remove the item.');
                exit;
            }
        }
        $this->getLayout()->getUpdate()->addHandle('checkout_icart_delete');
        $this->loadLayoutUpdates();
        $this->generateLayoutXml()->generateLayoutBlocks();
        $this->renderLayout();
    }
    
    public function removeWishlistAction()
    {
        $wishlist = $this->_getWishlist();
        $itemId = (int) $this->getRequest()->getParam('item');
        
        $item = Mage::getModel('wishlist/item')->load($itemId);

        if($item->getWishlistId()==$wishlist->getId()) {
            try {
                $item->delete();
                $wishlist->save();
            } catch (Exception $e) {
                echo $this->__('Cannot remove the item.');
                exit;
            }
        }

        Mage::helper('wishlist')->calculate();
                       
        $this->getLayout()->getUpdate()->addHandle('checkout_icart_delete');
        $this->loadLayoutUpdates();
        $this->generateLayoutXml()->generateLayoutBlocks();
        $this->renderLayout();
    }
    
    public function removeCompareAction()
    {                        
        
        if ($productId = (int) $this->getRequest()->getParam('product')) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);

            if($product->getId()) {
                $item = Mage::getModel('catalog/product_compare_item');
                if(Mage::getSingleton('customer/session')->isLoggedIn()) {
                    $item->addCustomerData(Mage::getSingleton('customer/session')->getCustomer());
                } else {
                    $item->addVisitorId(Mage::getSingleton('log/visitor')->getId());
                }

                $item->loadByProduct($product);

                if($item->getId()) {
                    $item->delete();                    
                    Mage::dispatchEvent('catalog_product_compare_remove_product', array('product'=>$item));
                    Mage::helper('catalog/product_compare')->calculate();
                }
            }
        }                                
                       
        $this->getLayout()->getUpdate()->addHandle('checkout_icart_delete');
        $this->loadLayoutUpdates();
        $this->generateLayoutXml()->generateLayoutBlocks();
        $this->renderLayout();
    }
    
    
    public function clearCompareAction()
    {                                                
        
        $items = Mage::getResourceModel('catalog/product_compare_item_collection')
            //->useProductItem(true)
            //->setStoreId(Mage::app()->getStore()->getId())
            ;

        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $items->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId());
        }
        else {
            $items->setVisitorId(Mage::getSingleton('log/visitor')->getId());
        }

        $session = Mage::getSingleton('catalog/session');
        /* @var $session Mage_Catalog_Model_Session */

        try {
            $items->clear();            
            Mage::helper('catalog/product_compare')->calculate();
        } catch (Exception $e) {
            echo $this->__('Cannot remove the item.');
            exit;
        }
        
                       
        $this->getLayout()->getUpdate()->addHandle('checkout_icart_delete');
        $this->loadLayoutUpdates();
        $this->generateLayoutXml()->generateLayoutBlocks();
        $this->renderLayout();
    }
    

}
