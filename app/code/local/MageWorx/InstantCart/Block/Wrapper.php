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

class MageWorx_InstantCart_Block_Wrapper extends Mage_Core_Block_Template
{
    public function getProduct() {
        return Mage::registry('product');
    }

    public function getTopLinkCart() {
        //$count = Mage::helper('checkout/cart')->getSummaryCount();
        // compatibility with customer credit
        if (Mage::getStoreConfig('checkout/cart_link/use_qty')) {
            $count = Mage::getSingleton('checkout/cart')->getItemsQty();
        } else {
            $count = Mage::getSingleton('checkout/cart')->getItemsCount();
        }
        
        if( $count == 1 ) {
            $text = Mage::helper('checkout')->__('My Cart (%s item)', $count);
        } elseif( $count > 0 ) {
            $text = Mage::helper('checkout')->__('My Cart (%s items)', $count);
        } else {
            $text = Mage::helper('checkout')->__('My Cart');
        }
        return $text;
    }
    
    public function getTopLinkWishlist(){
        //Mage::helper('wishlist')->calculate();                        
        $count = Mage::helper('wishlist')->getItemCount();
        
        if( $count == 1 ) {
            $text = Mage::helper('wishlist')->__('My Wishlist (%d item)', $count);
        } elseif( $count > 0 ) {
            $text = Mage::helper('wishlist')->__('My Wishlist (%d items)', $count);
        } else {
            $text = Mage::helper('wishlist')->__('My Wishlist');
        }
        return $text;
    }
    
    public function getUrl($route='', $params=array())
    {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') $params['_secure'] = true;
        return $this->_getUrlModel()->getUrl($route, $params);
    }
    
}