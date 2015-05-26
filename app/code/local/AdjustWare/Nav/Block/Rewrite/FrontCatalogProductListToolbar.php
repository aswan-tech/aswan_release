<?php
/**
 * Product:     Layered Navigation Pro for Enterprise Edition - 16/08/12
 * Package:     AdjustWare_Nav_10.4.9_10.0.0_557110
 * Purchase ID: hXgQU3oI8FOfJ8PDCk5s6h6XPM5oH2Kx0N6TCAPkqN
 * Generated:   2013-04-22 06:59:44
 * File path:   app/code/local/AdjustWare/Nav/Block/Rewrite/FrontCatalogProductListToolbar.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Nav')){ eAVqrcwyPjfMrDVe('8ccf3a80f8fca47564282a25578a3b33'); ?><?php
class AdjustWare_Nav_Block_Rewrite_FrontCatalogProductListToolbar extends Mage_Catalog_Block_Product_List_Toolbar
{
    
    protected function _beforeToHtml()
    {
    	$this->getRequest()->setParam('cat', '');
    	
        if(Mage::helper('adjnav')->isPageAutoload())
        {
            $this->setTemplate('adjnav/product_list_toolbar.phtml');
        }
        return parent::_beforeToHtml();
    }
    
    public function getLimit()
    {
        if (Mage::helper('adjnav')->isPageAutoload())
        {
            $mode = $this->getCurrentMode();
            $limit = Mage::getStoreConfig('design/adjnav_endless_page/products_on_' . $mode . '_page');
            if ($limit)
            {
                $this->setData('_current_limit', $limit);
                return $limit;
            }
        }
        return parent::getLimit();
    }   
}
 } ?>