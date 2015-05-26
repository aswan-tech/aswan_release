<?php
/**
 * Product:     Layered Navigation Pro for Enterprise Edition - 16/08/12
 * Package:     AdjustWare_Nav_10.4.9_10.0.0_557110
 * Purchase ID: hXgQU3oI8FOfJ8PDCk5s6h6XPM5oH2Kx0N6TCAPkqN
 * Generated:   2013-04-22 06:59:44
 * File path:   app/code/local/AdjustWare/Nav/Model/Catalog/Layer/Filter/Category.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Nav')){ BfOpaChkEeNmajOB('7a8822a63cd7feb084b1a869c79a47fc'); ?><?php

class AdjustWare_Nav_Model_Catalog_Layer_Filter_Category extends Mage_Catalog_Model_Layer_Filter_Category
{
    protected $cat = null;
    
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        // very small optimization
        $catId = (int) Mage::helper('adjnav')->getParam($this->getRequestVar());
        if ($catId){
            $request->setParam($this->getRequestVar(), $catId);
            parent::apply($request, $filterBlock);
        }
        
        $category = $this->getCategory();
        if (!Mage::registry('current_category_filter')) {
            Mage::register('current_category_filter', $category);
        }

        if (!isset($filter) || !$filter) {
            $this->addCategoryFilter($category, null);
            return $this;
        }

        $this->_appliedCategory = Mage::getModel('catalog/category')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($filter);

        if ($this->_isValidCategory($this->_appliedCategory)) {
            /*
            $this->getLayer()->getProductCollection()
                ->addCategoryFilter($this->_appliedCategory);
            */
			
            $this->addCategoryFilter($this->_appliedCategory, $filter);
            $this->getLayer()->getState()->addFilter(
                $this->_createItem($this->_appliedCategory->getName(), $filter)
            );
        }
        return $this;
    }

    public function getRootCategory()
    {
        if (is_null($this->cat)){
            $this->cat = Mage::getModel('catalog/category')
                ->load($this->getLayer()->getCurrentStore()->getRootCategoryId());
        }
        return $this->cat;
    }
    
 public function getSiblingCategories() // by dhananjay
    {
        $layer = Mage::getSingleton('catalog/layer');
        $category   = $layer->getCurrentCategory();
        /* @var $category Mage_Catalog_Model_Category */
		$category->getParentId();
		$parentCategory = Mage::getModel('catalog/category')->load($category->getParentId());
        $pcategories = $parentCategory->getChildrenCategories();
        $productCollection = Mage::getResourceModel('catalog/product_collection');
        $layer->prepareProductCollection($productCollection);
        $productCollection->addCountToCategories($pcategories);
        return $pcategories;
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
    protected function _getItemsData()
    {
        $key = $this->getLayer()->getStateKey().'_SUBCATEGORIES';
        $key .= Mage::helper('adjnav')->getCacheKey('cat');
        $pageKey  = Mage::getBlockSingleton('page/html_pager')->getPageVarName();
        $queryStr =  Mage::helper('adjnav')->getParams(true, $pageKey);
        $data = $this->getLayer()->getAggregator()->getCacheData($key);

        if ($data === null) {
            $category   = null;
            $showTopCategories = Mage::getStoreConfig('design/adjnav/top_cats');
            if ($showTopCategories)
                $category = $this->getRootCategory();
            else
                $category = $this->getCategory();
                
            /** @var $categoty Mage_Catalog_Model_Categeory */
            $categories = $category->getChildrenCategories();
			
			if (is_object($categories)) {
				$t = $categories->getData();
			}
			
			if(empty($t))
			   $categories = $this->getSiblingCategories();
#d($categories->getSelect()->__tostring());
            $data = array();
            $level = 0;
            $parent = null;
            if ($category->getLevel() > 1){ // current category is not root
                $parent = $category->getParentCategory();
                
                ++$level;
                if ($parent->getLevel()>1){
                    $data[] = array(
                        'label'       => $parent->getName(),
                        'value'       => $parent->getUrl(),
                        'level'       => $level,
                        'category_id' => $parent->getId(),
                        'uri'   => $queryStr,
                    );
                }
                //always include current category
                ++$level;
                /*$data[] = array(
                    'label'       => $category->getName(),
                    'value'       => '',
                    'level'       => $level,
                    'is_current'  => true,
                    'category_id' => $category->getId(),
                    'uri'   => $queryStr,
                );*/
            } 
            
            /*if (!$showTopCategories){
                $this->getLayer()->getProductCollection()
                    ->addCountToCategories($categories);
            }*/
            
             
            ++$level;
            foreach ($categories as $cat) {
               //echo $this->getProductCount($cat)."<br />";
			//if ($cat->getIsActive() && ($showTopCategories || $cat->getProductCount())) {
                //if ($cat->getIsActive() && $this->getProductCountCustom($cat) > 0) { //Commented for optimization
				//if ($cat->getIsActive() && $cat->getHptd()) {/////////////uncomment this if want to show cats which have salable products  ###Vishal
                if ($cat->getIsActive()) {
				//if ($cat->getIsActive()) {
                    $data[] = array(
                        'label'       => $cat->getName(),
                        'value'       => $cat->getId(), 
                       // 'count'       => $cat->getProductCount(),
                        'level'       => $level,
                        'category_id' => $cat->getId(),
                        'uri'         => $cat->getUrl(), 
                    );
                }
            }
            
            
            
if (Mage::getStoreConfig('design/adjnav/reset_filters'))
{
    $queryStr = '';
}
            
            for ($i=0, $n=sizeof($data); $i<$n; ++$i) {
                $url = $data[$i]['uri'];
                $pos = strpos($url, '?');
                if ($pos)
                    $url = substr($url, 0, $pos);
                $data[$i]['uri'] = $url . $queryStr;
            }
#d($this->getLayer()->getProductCollection()->getSelect()->__tostring());            
            $tags = $this->getLayer()->getStateTags();
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }
       // print_r($data);
        return $data;
    }    
    
    protected function _initItems()
    {
        $data = $this->_getItemsData();
        $items=array();
        foreach ($data as $itemData) {
            $obj = new Varien_Object();
            $obj->setData($itemData);
            $obj->setUrl($itemData['value']);
            
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
    
    /**
     * Apply category filter to product collection
     *
     * @param object $category
     * @param Mage_Catalog_Model_Layer_Filter_Category $filter
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Attribute
     */
    public function addCategoryFilter($category, $filter)
    {
    	if(!Mage::helper('adjnav')->isSolrEnabled())
    	{
    	    //do not do this if SOLR
    		return $this;	
    	}
    	
        $productCollection = $this->getLayer()->getProductCollection();
		if(version_compare(Mage::getVersion(),'1.12.0.0','>=' ))
		    $value = array(
            'category_ids' => $category->getId()
           );
		else
        $value = array(
            'categories' => $category->getId()
        );
        $productCollection->addFqFilter($value);

        return $this;
    }
    
    public function getFilterCategory(Zend_Controller_Request_Abstract $request)
    {
        $filter = (int) $request->getParam($this->getRequestVar());
        if (!$filter) {
            return $filter;
        }
        $this->_categoryId = $filter;
        
        $category = $this->getCategory();
        return $category;
    }
    
} } 