<?php
/**
* @copyright Amasty.
*/
class Amasty_Stockstatus_Model_Rewrite_Sales_Quote_Item extends Mage_Sales_Model_Quote_Item
{
    public function getMessage($string = true)
    {
            if ('checkout' == Mage::app()->getRequest()->getModuleName() && Mage::getStoreConfig('catalog/general/displayincart'))
            {
                $product = Mage::getModel('catalog/product')->load($this->getProduct()->getId());
                $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
                if ( (!Mage::getStoreConfig('catalog/general/displayforoutonly') || !$product->isSaleable()) || ($product->isInStock() && $stockItem->getData('qty') <= Mage::helper('amstockstatus')->getBackorderQnt() ) )
                {
                    if (Mage::helper('amstockstatus')->getCustomStockStatusText($product))
                    {
                        $this->addMessage(Mage::helper('amstockstatus')->getCustomStockStatusText($product));
                    }
                }
            }
        return parent::getMessage($string);
    }
}