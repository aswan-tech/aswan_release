<?php

class FCM_Categorycode_Model_Hidecategories
    extends Mage_Catalog_Model_Resource_Category_Flat
{
    /**
     * Load nodes by parent id
     *
     * @param unknown_type $parentNode
     * @param integer $recursionLevel
     * @param integer $storeId
     * @return Mage_Catalog_Model_Resource_Category_Flat
     */
    protected function _loadNodes($parentNode = null, $recursionLevel = 0, $storeId = 0)
    {
        $nodes = parent::_loadNodes($parentNode, $recursionLevel, $storeId);
 
        foreach ($nodes as $node) {		
			if ($node->getLevel() != 2) {
                if (strtolower($node->getName()) != 'get the look' && strtolower($node->getName()) != 'new arrivals') {
				    //if ($node->getProductCount() == 0) {
					//if ($this->getProductCountExcludeOutStock($node) == 0) {
					if ($this->getProductCountCustom($node) <= 0) {					
						unset($nodes[$node->getId()]);
					}
                }
            }
        }
        return $nodes;
    }
	
	/**
     * Get products count in category
     *
     * @param Mage_Catalog_Model_Category $category
     * @return integer
     */
    public function getProductCountExcludeOutStock($category)
    {
		$read = $this->_getReadAdapter();
		
		$store_data =   Mage::getModel('core/store')->load($category->getStoreId()); //load store object
		$website_id = $store_data->getWebsiteId();	//get website id from the store		

		$select =  $read->select()
            ->from(
                 array('main_table' =>  $this->getTable('catalog/category_product')),
                "COUNT(main_table.product_id)"
            )
			->joinLeft(
                array('stock'=>$this->getTable('cataloginventory/stock_status')),
                'main_table.product_id=stock.product_id AND '.
                $read->quoteInto('stock.website_id=? ',
                $website_id ),
                array())
            ->where("main_table.category_id = ?", $category->getId())
			->where("round(stock.qty) > 0 ")
			->where("stock.stock_status = ? ", 1)
            ->group("main_table.category_id");
			
			//echo $select->__toString(); exit;
        return (int) $read->fetchOne($select);
    }
	
	public function getProductCountCustom($category)
	{
		$cur_category = Mage::getModel('catalog/category')->load($category->getId());
		$layer = Mage::getSingleton('catalog/layer');
		$layer->setCurrentCategory($cur_category);
		$_productCollectionCustom = $layer->getProductCollection(); 
		if(is_object(Mage::registry('current_category')))
		 $layer->setCurrentCategory(Mage::getModel("catalog/category")->load(Mage::registry('current_category')->getEntityId()));

		if(sizeof($_productCollectionCustom->getData()) > 0) {
			return 1;
		} else {
			return 0;
		}
	} 
}
