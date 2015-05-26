<?php
/**
 * Product:     Layered Navigation Pro for Enterprise Edition - 16/08/12
 * Package:     AdjustWare_Nav_10.4.9_10.0.0_557110
 * Purchase ID: hXgQU3oI8FOfJ8PDCk5s6h6XPM5oH2Kx0N6TCAPkqN
 * Generated:   2013-04-22 06:59:44
 * File path:   app/code/local/AdjustWare/Nav/Model/Catalog/Layer/Filter/Categorysearch.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Nav')){ ZCDimqpgBahemrDZ('58b4f61ca877005fd028941e332ea581'); ?><?php

class AdjustWare_Nav_Model_Catalog_Layer_Filter_Categorysearch extends Mage_Catalog_Model_Layer_Filter_Category
{
    protected function _getItemsData()
    {
        if (!isset($queryStr))
		{
			$queryStr = '';
		}
		$key = $this->getLayer()->getStateKey().'_SEARCH_SUBCATEGORIES';
        $key .= Mage::helper('adjnav')->getCacheKey('cat');
        $data = $this->getLayer()->getAggregator()->getCacheData($key);
        if(Mage::helper('adjnav')->isSolrEnabled())
        {
		    if(version_compare(Mage::getVersion(),'1.12.0.0','>=' ))
                $facets = $this->getLayer()->getProductCollection()->getFacetedData('category_ids');    
			else
            $facets = $this->getLayer()->getProductCollection()->getFacetedData('categories');    
        }
        if ($data === null) {
            $category   = $this->getCategory();
            
            /** @var $categoty Mage_Catalog_Model_Categeory */
            if ($category->getLevel() == 1) {
				$c = $category->getChildrenCategories();
				$categories = new Varien_Data_Collection();
				
				foreach ($c as $ct) {
					$childs = $ct->getChildrenCategories();
					$categories->addItem($ct);
					
					foreach ($childs  as $ct2) {
						$categories->addItem($ct2);
					}
				}
				
			} else {
				$categories = $category->getChildrenCategories();
			}

            $data = array();
            $level = 0;
//            $parentId = 0;
            if ($category->getLevel() > 1){ // current category is not root
                $parent = $category->getParentCategory();
                
				//$lvCats = new Varien_Data_Collection();
                //$lvCats->addItem($parent);
				//$lvCats->addItem($category);
				//$this->getLayer()->getProductCollection()
                //->addCountToCategories($lvCats);
                ++$level;
                if ($parent->getLevel()>1){
                    $data[] = array(
                        'label' => $parent->getName(),
                        'value' => $parent->getId(),
                        'count' => 0,
                        'level' => $level,
                        'uri'   => $queryStr,
                    );
//                    $parentId = $parent->getId();
//                    $categories->addItem($parent);
                    
                }
                //always include current category
                ++$level;
                $data[] = array(
                    'label' => $category->getName(),
                    'value' => '',
                    'level' => $level,
                    'is_current' => true,
                    'uri'   => $queryStr,
                );
            }
             
            $this->getLayer()->getProductCollection()
                ->addCountToCategories($categories);
                
//            if ($parentId){
//                $data[0]['count'] = $parent->getProductCount();
//                $categories->removeItemByKey($parentId);
//            }    
            
            ++$level;
            foreach ($categories as $cat) {
            	if(Mage::helper('adjnav')->isSolrEnabled())
            	{
	            	$categoryId = $cat->getId();
	            	if(!empty($facets[$categoryId]))
	            	{
	                    $cat->setProductCount($facets[$categoryId]);
	                } else {
	                    $cat->setProductCount(0);
	                }            		
            	}

                if ($cat->getIsActive() && $cat->getProductCount()) {
                     $data[] = array( 
                        'label'       => $cat->getName(),
                        'value'       => $cat->getId(), 
                        'count'       => $cat->getProductCount(),
                        'level'       => $level,
                        'category_id' => $cat->getId(),
                        'uri'         => $cat->getUrl(),
                    );
                }
            }
            $tags = $this->getLayer()->getStateTags();
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }
 if (Mage::getStoreConfig('design/adjnav/reset_filters'))
{
    $queryStr = '';
}
    $pageKey  = Mage::getBlockSingleton('page/html_pager')->getPageVarName();
    $queryStr =  Mage::helper('adjnav')->getParams(true, $pageKey);            
            for ($i=0, $n=sizeof($data); $i<$n; ++$i) {
                $url = $data[$i]['uri'];
                $pos = strpos($url, '?');
                if ($pos)
                    $url = substr($url, 0, $pos);
                $data[$i]['uri'] = $url . $queryStr;
            }
        return $data;
    }
    
    public function getLayer()
    {
        $layer = $this->_getData('layer');
        if (is_null($layer)) {
            $layer = Mage::getSingleton(Mage::helper('adjnav')->getLayerModelName());
            $this->setData('layer', $layer);
        }
        return $layer;
    }

    protected function _initItems()
    {
        $data  = $this->_getItemsData();
        $items = array();
        foreach ($data as $itemData) {
            $obj = Mage::getModel('catalog/layer_filter_item');
            $obj->setData($itemData);
            $obj->setFilter($this);
            
            $items[] = $obj;
        }
        $this->_items = $items;
        return $this;
    }  
    
    public function addFacetCondition()
    {
        $category = $this->getCategory();
        $childrenCategories = $category->getChildrenCategories();

        $useFlat = (bool)Mage::getStoreConfig('catalog/frontend/flat_catalog_category');
        $categories = ($useFlat)
            ? array_keys($childrenCategories)
            : array_keys($childrenCategories->toArray());

		if(version_compare(Mage::getVersion(),'1.12.0.0','>=' ))
            $this->getLayer()->getProductCollection()->setFacetCondition('category_ids', $categories);
		else
            $this->getLayer()->getProductCollection()->setFacetCondition('categories', $categories);

        return $this;
    }  
} } 