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
 * @copyright  Copyright (c) 2010 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Instant Cart extension
 *
 * @category   MageWorx
 * @package    MageWorx_InstantCart
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

class MageWorx_InstantCart_Helper_Data extends Mage_Core_Helper_Url
{
    
    const XML_ENABLED = 'mageworx_customers/icart/enabled';
    const XML_SHOW_PRODUCT_BLOCK = 'mageworx_customers/icart/show_products_block';        
    
    public function isEnabled() {
        return Mage::getStoreConfig(self::XML_ENABLED);        
    }
    
    public function getShowProductsBlock() {
        return Mage::getStoreConfig(self::XML_SHOW_PRODUCT_BLOCK);        
    }    
    
    public function getAddUrl($product, $additional = array())
    {
        $continueUrl    = Mage::helper('core')->urlEncode($this->getCurrentUrl());
        $urlParamName   = Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED;

        $routeParams = array(
            $urlParamName   => $continueUrl,
            'product'       => $product->getEntityId()
        );

        if (!empty($additional)) {
            $routeParams = array_merge($routeParams, $additional);
        }

        if ($product->hasUrlDataObject()) {
            $routeParams['_store'] = $product->getUrlDataObject()->getStoreId();
            $routeParams['_store_to_url'] = true;
        }

        if ($this->_getRequest()->getRouteName() == 'checkout'
            && $this->_getRequest()->getControllerName() == 'cart') {
            $routeParams['in_cart'] = 1;
        }

        return $this->_getUrl('checkout/icart/add/', $routeParams);
    }

    public function isExtensionEnabled($extension)
    {
        return (string)Mage::getConfig()->getModuleConfig($extension)->active == 'true';
    }
}