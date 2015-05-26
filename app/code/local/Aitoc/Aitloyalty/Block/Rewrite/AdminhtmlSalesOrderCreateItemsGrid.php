<?php
/**
 * Product:     Loyalty Program for Enterprise Edition
 * Package:     Aitoc_Aitloyalty_10.0.10_574534
 * Purchase ID: INzRIwyyaNoeOLERhAgt4U28qVKIeEa3dfPrgaAN3C
 * Generated:   2013-05-13 06:36:55
 * File path:   app/code/local/Aitoc/Aitloyalty/Block/Rewrite/AdminhtmlSalesOrderCreateItemsGrid.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitloyalty')){ jSgmMMjIEyjZUkcE('67e8ec211f2c6d27397a0cd832b7e2b7'); ?><?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitloyalty_Block_Rewrite_AdminhtmlSalesOrderCreateItemsGrid extends Mage_Adminhtml_Block_Sales_Order_Create_Items_Grid
{
	protected function _afterToHtml($html)
	{
		$html = str_replace('<th class="no-link">' . Mage::helper('sales')->__('Discount') . '</th>', '<th class="no-link">' . Mage::helper('sales')->__('Discount/Surcharge') . '</th>', $html);
		return $html;
	}
} } 