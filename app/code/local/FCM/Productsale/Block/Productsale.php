<?php
class FCM_Productsale_Block_Productsale extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getProductsale()     
     { 
        $saleProductCollection = Mage::getSingleton('catalog/product')->getCollection()->addAttributeToFilter('status', 1)->addAttributeToFilter('visibility', 4)->addAttributeToFilter('is_sale', 1);
        return $saleProductCollection;
        
    }
}