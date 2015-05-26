<?php
/**
* @copyright Amasty.
*/
class Amasty_Stockstatus_Block_Status extends Mage_Core_Block_Template
{
    protected $_product = null;
    
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('amstockstatus/catalog_product_list_stockstatus.phtml');
    }
    
    public function setProduct($product)
    {
        $this->_product = Mage::getModel('catalog/product')->load($product->getId());
        return $this;
    }
    
    public function getProduct()
    {
        return $this->_product;
    }
    
    public function getCustomStockStatus()
    {
        $product = $this->getProduct();
        $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
        if ( (!Mage::getStoreConfig('catalog/general/displayforoutonly') || !$product->isSaleable()) || ($product->isInStock() && $stockItem->getData('qty') <= Mage::helper('amstockstatus')->getBackorderQnt() ) )
        {
            return Mage::helper('amstockstatus')->getCustomStockStatusText($product);
        }
        return '';
    }
    
    public function getStyle()
    {
        return 'amstockstatus_' . Mage::helper('amstockstatus')->getCustomStockStatusId($this->getProduct());
    }
    
    public function getAddToCartUrl($product, $additional = array())
    {
        if ($product->getTypeInstance(true)->hasRequiredOptions($product)) {
            $url = $product->getProductUrl();
            $link = (strpos($url, '?') !== false) ? '&' : '?';
            return $url . $link . 'options=cart';
        }
        return $this->helper('checkout/cart')->getAddUrl($product, $additional);
    }
}