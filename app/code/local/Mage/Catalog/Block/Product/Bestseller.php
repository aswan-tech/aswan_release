<?php
/**
 * Catalog data product best seller
 *
 * @category   Mage
 * @package    Mage_Catalog_Product
 */

class Mage_Catalog_Block_Product_Bestseller extends Mage_Catalog_Block_Product_Abstract{
    public function __construct(){
        parent::__construct();
        $storeId = Mage::app()->getStore()->getId();
        $products = Mage::getResourceModel('reports/product_collection')
            ->addOrderedQty()
            ->addAttributeToSelect('*')
            ->addAttributeToSelect(array('name', 'price', 'small_image'))
            ->setStoreId($storeId)
            ->addStoreFilter($storeId)
            ->setOrder('ordered_qty', 'desc'); // most best sellers on top
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
 
        //$products->setPageSize(3)->setCurPage(1);
        $this->setProductCollection($products);
    }
}