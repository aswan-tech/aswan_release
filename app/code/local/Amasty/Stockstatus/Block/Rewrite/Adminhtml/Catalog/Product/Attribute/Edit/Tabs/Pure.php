<?php
/**
* @copyright Amasty.
*/
if ('true' == (string)Mage::getConfig()->getNode('modules/Amasty_Conf/active'))
{
    class Amasty_Stockstatus_Block_Rewrite_Adminhtml_Catalog_Product_Attribute_Edit_Tabs_Pure extends Amasty_Conf_Block_Adminhtml_Catalog_Product_Attribute_Edit_Tabs {}
} else 
{
    class Amasty_Stockstatus_Block_Rewrite_Adminhtml_Catalog_Product_Attribute_Edit_Tabs_Pure extends Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tabs {}
}