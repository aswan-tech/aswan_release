<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2011 Amasty (http://www.amasty.com)
* @package Amasty_Stockstatus
*/
class Amasty_Stockstatus_Block_Bundle_Catalog_Product_View_Type_Bundle_Option_Checkbox extends Amasty_Stockstatus_Block_Bundle_Catalog_Product_View_Type_Bundle_Option
{
    public function _construct()
    {
        $this->setTemplate('bundle/catalog/product/view/type/bundle/option/checkbox.phtml');
    }
}