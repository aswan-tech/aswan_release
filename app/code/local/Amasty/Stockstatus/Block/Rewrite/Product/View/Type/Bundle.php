<?php
/**
* @copyright Amasty.
*/
class Amasty_Stockstatus_Block_Rewrite_Product_View_Type_Bundle extends Mage_Bundle_Block_Catalog_Product_View_Type_Bundle
{
    protected function _afterToHtml($html)
    {
        $html = parent::_afterToHtml($html);
        $html = $this->helper('amstockstatus')->processViewStockStatus($this->getProduct(), $html);
        return $html;
    }
}