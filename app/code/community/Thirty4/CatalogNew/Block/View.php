<?php

class Thirty4_CatalogNew_Block_View extends Mage_Core_Block_Template
{
	protected function _prepareLayout()
	{
		parent::_prepareLayout();
		
		return $this;
	}
	
	protected function _getProductCollection()
	{
		if (is_null($this->_productCollection)) {
			$this->_productCollection = Mage::getSingleton('catalognew/layer')->getProductCollection();
		}
		
		return $this->_productCollection;
	}
	
	public function setListCollection()
	{
		$productList = $this->getChild('new_product_list');
		$productList->setCollection($this->_getProductCollection());
	}
	
	public function IsRssCatalogEnable()
	{
		$path = Mage_Rss_Block_List::XML_PATH_RSS_METHODS.'/catalog/new';
		
		return (bool)Mage::getStoreConfig($path);
	}
	
	public function getRssLink()
	{
        $param = array('sid' => $this->getCurrentStoreId());
    
        return Mage::getUrl(Mage_Rss_Block_List::XML_PATH_RSS_METHODS.'/catalog/new', $param);
	}
	
	public function getProductListHtml()
	{
		return $this->getChildHtml('new_product_list');
	}
}
