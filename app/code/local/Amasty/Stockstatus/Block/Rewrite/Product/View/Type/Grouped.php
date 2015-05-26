<?php
/**
* @copyright Amasty.
*/
class Amasty_Stockstatus_Block_Rewrite_Product_View_Type_Grouped extends Mage_Catalog_Block_Product_View_Type_Grouped
{
    protected function _toHtml()
    {
        $this->setTemplate('amstockstatus/grouped.phtml');
        return parent::_toHtml();
    }
    
    protected function _afterToHtml($html)
    {
        $html = parent::_afterToHtml($html);
        $html = $this->helper('amstockstatus')->processViewStockStatus($this->getProduct(), $html);
        return $html;
    }
    
    public function getStockStatus($product)
    {
        return $this->helper('amstockstatus')->getCustomStockStatusText($product);
    }
}