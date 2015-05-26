<?php
/**
* @copyright Amasty.
*/
class Amasty_Stockstatus_Block_Rewrite_Product_View_Type_Virtual extends Mage_Catalog_Block_Product_View_Type_Virtual
{
    protected function _afterToHtml($html)
    {
        $html = parent::_afterToHtml($html);
        $html = $this->helper('amstockstatus')->processViewStockStatus($this->getProduct(), $html);
        return $html;
    }
}