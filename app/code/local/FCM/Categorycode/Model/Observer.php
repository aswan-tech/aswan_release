<?php

/**
 * Hide Empty Categories
 *
 * @category    FCM
 * @package     FCM_Categorycode_Model_Observer
 * @copyright   Copyright (c) 2011 FCM
 * @author      Shikha Raina
 */

/**
 * Event Observer
 *
 * @category    FCM
 * @package     FCM_Categorycode_Model_Observer
 */
class FCM_Categorycode_Model_Observer {

	public function categoryLoadAfter($observer)
	{
		if(Mage::registry('current_category'))
		{
			if(Mage::registry('current_category')->getUrlKey() == 'get-the-look')
			{
				$update = Mage::getSingleton('core/layout')->getUpdate();
				$update->addHandle('catalog_product_new_handle');
			}
		}
		
	}

    /**
     * Remove hidden caegories from the collection
     *
     * @param Varien_Event_Observer $observer
     */
    public function catalogCategoryCollectionLoadAfter($observer) {
        if ($this->_isApiRequest())
            return;
		if((isset($_SERVER['HTTP_X_REQUESTED_WITH'])) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'))
		return;
        $collection = $observer->getEvent()->getCategoryCollection();
        $this->_removeHiddenCollectionItems($collection);
    }

    /**
     * Remove hidden items from a product or category collection
     *
     * @param Mage_Eav_Model_Entity_Collection_Abstract|Mage_Core_Model_Mysql4_Collection_Abstract $collection
     */
    public function _removeHiddenCollectionItems($collection) {		  
        // Loop through each category or product
              foreach ($collection as $key => $item) {
			  $catData = Mage::getModel("catalog/category")->load($item->getEntityId());
            // If it is a category
            if ($item->getEntityTypeId() == 3 && $item->getLevel() != 2) {
                if (strtolower($catData->getUrlKey()) != 'get-the-look' && strtolower($catData->getUrlKey()) != 'new-arrivals') {
				    if ($this->getProductCountCustom($catData) <= 0) {
                        $collection->removeItemByKey($key);
                    }
                }
            }
        }	             
    }

    /**
     * Return true if the reqest is made via the api
     *
     * @return boolean
     */
    protected function _isApiRequest() {
        return Mage::app()->getRequest()->getModuleName() === 'api';
    }
	
	 public function getProductCountCustom($category)
		{
		        $cur_category = Mage::getModel('catalog/category')->load($category->getId());
				$layer = Mage::getSingleton('catalog/layer');
				$layer->setCurrentCategory($cur_category);
				$_productCollectionCustom = $layer->getProductCollection(); 
				if(is_object(Mage::registry('current_category')))
                 $layer->setCurrentCategory(Mage::getModel("catalog/category")->load(Mage::registry('current_category')->getEntityId()));

				if(sizeof($_productCollectionCustom->getData()) > 0)
					return 1;
				else
					return 0;
		} 
}
