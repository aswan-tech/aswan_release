<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2011 Amasty (http://www.amasty.com)
* @package Amasty_Stockstatus
*/
class Amasty_Stockstatus_Block_Bundle_Catalog_Product_View_Type_Bundle_Option extends Mage_Bundle_Block_Catalog_Product_View_Type_Bundle_Option
{
    public function getSelectionQtyTitlePrice($_selection, $includeContainer = true)
    {
        $price = $this->getProduct()->getPriceModel()->getSelectionPreFinalPrice($this->getProduct(), $_selection);
        
        $stockStatus = Mage::helper('amstockstatus')->getCustomStockStatusText(Mage::getModel('catalog/product')->load($_selection->getId()));
        if ($stockStatus)
        {
            $stockStatus = '(' . $stockStatus . ') &nbsp; ';
        }
        
        return $_selection->getSelectionQty()*1 . ' x ' . $_selection->getName() . ' &nbsp; ' . $stockStatus .
            ($includeContainer ? '<span class="price-notice">':'') . '+' .
            $this->formatPriceString($price, $includeContainer) . ($includeContainer ? '</span>':'');
    }
    
    public function getSelectionTitlePrice($_selection, $includeContainer = true)
    {
        $price = $this->getProduct()->getPriceModel()->getSelectionPreFinalPrice($this->getProduct(), $_selection, 1);
        
        $stockStatus = Mage::helper('amstockstatus')->getCustomStockStatusText(Mage::getModel('catalog/product')->load($_selection->getId()));
        if ($stockStatus)
        {
            $stockStatus = '(' . $stockStatus . ') &nbsp; ';
        }
        
        return $_selection->getName() . ' &nbsp; ' . $stockStatus . ($includeContainer ? '<span class="price-notice">':'') . '+' .
            $this->formatPriceString($price, $includeContainer) . ($includeContainer ? '</span>':'');
    }
}