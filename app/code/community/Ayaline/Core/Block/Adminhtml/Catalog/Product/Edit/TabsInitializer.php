<?php
/**
 * created : 2 juil. 2012
 * 
 * @category Ayaline
 * @package Ayaline_XXXX
 * @author aYaline
 * @copyright Ayaline - 2012 - http://magento-shop.ayaline.com
 * @license http://shop.ayaline.com/magento/fr/conditions-generales-de-vente.html
 */

class Ayaline_Core_Block_Adminhtml_Catalog_Product_Edit_TabsInitializer extends Mage_Core_Block_Abstract {
	
	protected function _prepareLayout() {
		$product = Mage::registry('product');
		
		if(!($setId = $product->getAttributeSetId())) {
			$setId = $this->getRequest()->getParam('set', null);
		}
		
		if($setId) {
			/* @var $block Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs */
			$block = $this->getLayout()->getBlock('product_tabs');
			
			if(!$block) {
				// This case occurs for example during the first step of configurable product creation
				return parent::_prepareLayout();
			}
						
			$data = array(
				'tabs'		=>	$block,
				'request'	=>	$this->getRequest(),
			);
			Mage::dispatchEvent('ayaline_core_adminhtml_catalog_product_tabs_initializer', $data);
			
		}
		return parent::_prepareLayout();
	}
    
}