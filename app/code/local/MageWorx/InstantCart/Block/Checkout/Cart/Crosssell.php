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
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mageworx.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 * or send an email to sales@mageworx.com
 *
 * @category   MageWorx
 * @package    MageWorx_InstantCart
 * @copyright  Copyright (c) 2011 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Instant Cart extension
 *
 * @category   MageWorx
 * @package    MageWorx_InstantCart
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

class MageWorx_InstantCart_Block_Checkout_Cart_Crosssell extends Mage_Checkout_Block_Cart_Crosssell
{
    public function getAddToCartUrl($product, $additional = array()) {
        if ($this->helper('icart')->isEnabled()) {
            return $this->helper('icart')->getAddUrl($product, $additional);
        } else {
            return parent::getAddToCartUrl($product, $additional);
        }
    }
    
    protected function _getCollection() {
        switch ($this->helper('icart')->getShowProductsBlock()) {
            case 'related':
                $collection = Mage::getModel('catalog/product_link')->useRelatedLinks()
                    ->getProductCollection()
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->addStoreFilter()
                    ->setPageSize($this->_maxItemCount);
                break;
            case 'up-sells':
                $collection = Mage::getModel('catalog/product_link')->useUpSellLinks()
                    ->getProductCollection()
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->addStoreFilter()
                    ->setPageSize($this->_maxItemCount);
                break;
            case 'cross-sells':
            default:    
               $collection = Mage::getModel('catalog/product_link')->useCrossSellLinks()
                    ->getProductCollection()
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->addStoreFilter()
                    ->setPageSize($this->_maxItemCount);                            
                break;                       
        }                   
        
        $this->_addProductAttributesAndPrices($collection);

        Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);

        return $collection;
    }

    protected function _getCartProductIds() {
        if ($id = Mage::app()->getRequest()->getParams('product')) {
            return $id;
        } else {
            return parent::_getCartProductIds();
        }
    }
    
}
