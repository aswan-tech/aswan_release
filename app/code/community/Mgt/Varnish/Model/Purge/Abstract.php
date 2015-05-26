<?php
/**
 * MGT-Commerce GmbH
 * http://www.mgt-commerce.com
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@mgt-commerce.com so we can send you a copy immediately.
 *
 * @category    Mgt
 * @package     Mgt_Varnish
 * @author      Stefan Wieczorek <stefan.wieczorek@mgt-commerce.com>
 * @copyright   Copyright (c) 2012 (http://www.mgt-commerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mgt_Varnish_Model_Purge_Abstract
{
    protected function _getVarnishModel()
    {
        return Mage::getModel('mgt_varnish/varnish');
    }
    
    protected function _getAdminSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }
    
    public function canPurge()
    {
        return $this->isEnabled();
    }
    
    static public function isEnabled()
    {
        return Mage::helper('mgt_varnish')->isEnabled();
    }
    
    protected function _getUrlRewriteCollection()
    {
        return Mage::getResourceModel('mgt_varnish/core_url_rewrite_collection');
    }
    
    protected function _getProductRelationCollection()
    {
        return Mage::getResourceModel('mgt_varnish/catalog_product_relation_collection');
    }
    
    protected function _getCategoryProductCollection()
    {
        return Mage::getResourceModel('mgt_varnish/catalog_category_product_collection');
    }
}