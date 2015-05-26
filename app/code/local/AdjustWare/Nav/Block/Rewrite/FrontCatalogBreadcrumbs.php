<?php
/**
 * Product:     Layered Navigation Pro for Enterprise Edition - 16/08/12
 * Package:     AdjustWare_Nav_10.4.9_10.0.0_557110
 * Purchase ID: hXgQU3oI8FOfJ8PDCk5s6h6XPM5oH2Kx0N6TCAPkqN
 * Generated:   2013-04-22 06:59:44
 * File path:   app/code/local/AdjustWare/Nav/Block/Rewrite/FrontCatalogBreadcrumbs.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Nav')){ BfOpaChkEeNmajOB('13f6a1112ca163662e2a4de39ef5a1df'); ?><?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
/**
 * Catalog breadcrumbs
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class AdjustWare_Nav_Block_Rewrite_FrontCatalogBreadcrumbs extends Mage_Catalog_Block_Breadcrumbs
{
    /**
     * Preparing layout
     *
     * @return Mage_Catalog_Block_Breadcrumbs
     */
    protected function _prepareLayout()
    {
        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbsBlock->addCrumb('home', array(
                'label'=>Mage::helper('catalog')->__('Home'),
                'title'=>Mage::helper('catalog')->__('Go to Home Page'),
                'link'=>Mage::getBaseUrl()
            ));

            $title = array();
            
            $prevCategory       = Mage::registry('current_category');
            $this->_getCategory();
                        
            $path  = Mage::helper('catalog')->getBreadcrumbPath();
            
            $this->_resetCategoryFilter($prevCategory);

            foreach ($path as $name => $breadcrumb) {
                $breadcrumbsBlock->addCrumb($name, $breadcrumb);
                $title[] = $breadcrumb['label'];
            }

            if ($headBlock = $this->getLayout()->getBlock('head')) {
                $headBlock->setTitle(join($this->getTitleSeparator(), array_reverse($title)));
            }
        }
        return parent::_prepareLayout();
    }
    
    protected function _getCategory()
    {
        $filter = Mage::getModel('adjnav/catalog_layer_filter_category')->setLayer($this->getLayer());
        $category = $filter->getFilterCategory($this->getRequest());
        
        if ( $category )
        {
            Mage::unregister('current_category');
            Mage::register('current_category', $category );
        }
        
        return $this;
    }
    
    protected function _resetCategoryFilter($category)
    {
        Mage::unregister('current_category');
        if ( $category )
        {
            Mage::register('current_category', $category);
        }
        
    }
} } 