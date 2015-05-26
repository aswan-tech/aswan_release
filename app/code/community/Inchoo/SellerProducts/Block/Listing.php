<?php
/**
 * @category     Inchoo
 * @package     Inchoo Seller Products
 * @author        Domagoj Potkoc, Inchoo Team <web@inchoo.net>
 * @modified    Mladen Lotar <mladen.lotar@surgeworks.com>, Vedran Subotic <vedran.subotic@surgeworks.com>
 */
class Inchoo_SellerProducts_Block_Listing extends Mage_Catalog_Block_Product_Abstract
{
	/*
	 * Check sort option and limits set in System->Configuration and apply them
	 * Additionally, set template to block so call from CMS will look like {{block type="sellerproducts/listing"}}
	 */
	public function __construct()
	{
		$this->setTemplate('inchoo/sellerproducts/block_seller_products.phtml');
		$currentCategory = Mage::registry('current_category');
		
		if(!$currentCategory) {
			$this->setLimit((int)Mage::getStoreConfig("sellerproducts/general/number_of_items"));
			$sort_by = Mage::getStoreConfig("sellerproducts/general/product_sort_by");
		} else {		
			$this->setLimit((int)Mage::getStoreConfig("sellerproducts/general/number_of_items_cat"));
			$sort_by = Mage::getStoreConfig("sellerproducts/general/product_sort_by_cat");		
		}
		
		$this->setItemsPerRow((int)Mage::getStoreConfig("sellerproducts/general/number_of_items_per_row"));

		switch ($sort_by) {
			case 0:
				$this->setSortBy("rand()");
			break;
			case 1:
				$this->setSortBy("created_at desc");
			break;
			default:
				$this->setSortBy("rand()");
		}
	}

	/*
	 * Load seller products collection
	 * */
	protected function _beforeToHtml()
	{	
		$currentCategory = Mage::registry('current_category');
		if($currentCategory) {
			$catid = $currentCategory->getId();
		}
		
		if(isset($catid)) {
			$category = Mage::getModel('catalog/category')->load($catid);		
			$collection = $category->getProductCollection();
		} else {		
			$collection = Mage::getResourceModel('catalog/product_collection');
		}
			$attributes = Mage::getSingleton('catalog/config')
				->getProductAttributes();

			$collection->addAttributeToSelect($attributes)
				->addMinimalPrice()
				->addFinalPrice()
				->addTaxPercents()
				->addAttributeToFilter('inchoo_seller_product', 1, 'left')
				->addStoreFilter()
				->getSelect()->order($this->getSortBy())->limit($this->getLimit());

			Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
			Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
		
			$this->_productCollection = $collection;

		$this->setProductCollection($collection);
		return parent::_beforeToHtml();
	}

	/*
	 * Return label for CMS block output
	 * */
	protected function getBlockLabel()
	{
		return $this->helper('sellerproducts')->getCmsBlockLabel();
	}

}