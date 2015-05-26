<?php

class Thirty4_CatalogNew_Model_Layer extends Mage_Catalog_Model_Layer
{
  protected function _getStoreId()
  {
    $storeId = null;
    if($storeId == null) {
      $storeId = Mage::app()->getStore()->getId();
    }
    return $storeId;
  }

  protected function _getCustomerGroupId()
  {
    $custGroupID = null;
    if($custGroupID == null) {
      $custGroupID = Mage::getSingleton('customer/session')->getCustomerGroupId();
    }
    return $custGroupID;
  }
  
	public function getProductCollection()
	{
		if (is_null($this->_productCollection)) {
            $storeId = $this->_getStoreId();
            $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
      
            
	        $product = Mage::getModel('catalog/product');
	        $todayDate = $product->getResource()->formatDate(time(), false);
	
          $productAttributes = array_flip(Mage::getSingleton('catalog/config')->getProductAttributes());
          unset($productAttributes['price_type']);
          unset($productAttributes['price_view']);
          unset($productAttributes['special_price']);
          unset($productAttributes['special_from_date']);
          unset($productAttributes['special_to_date']);
          $productAttributes = array_flip($productAttributes);
	        
	        $products = $product->getCollection()
	            ->setStoreId($storeId)
	            ->addStoreFilter()
	            ->addAttributeToFilter('news_from_date', array('date'=>true, 'to'=> $todayDate))
	            ->addAttributeToFilter(array(array('attribute'=>'news_to_date', 'date'=>true, 'from'=>$todayDate), array('attribute'=>'news_to_date', 'is' => new Zend_Db_Expr('null'))),'','left')
	            ->addAttributeToSort('news_from_date','desc')
	            ->addAttributeToSelect($productAttributes, 'left')
	            ->addAttributeToSelect(array('special_price', 'special_from_date', 'special_to_date'), 'left')
	        ;
	
	        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
	        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
	        
	        $this->_productCollection = $products;
		}
		
		return $this->_productCollection;
	}
}

