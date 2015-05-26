<?php
/**
 * Product:     Layered Navigation Pro for Enterprise Edition - 16/08/12
 * Package:     AdjustWare_Nav_10.4.9_10.0.0_557110
 * Purchase ID: hXgQU3oI8FOfJ8PDCk5s6h6XPM5oH2Kx0N6TCAPkqN
 * Generated:   2013-04-22 06:59:44
 * File path:   app/code/local/AdjustWare/Nav/Block/Catalog/Layer/View.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Nav')){ BfOpaChkEeNmajOB('9329f3fa5998d1b0dd067b4251f06b7e'); ?><?php
class AdjustWare_Nav_Block_Catalog_Layer_View extends AdjustWare_Nav_Block_Rewrite_EnterpriseSearchCatalogLayerView//AdjustWare_Nav_Block_Rewrite_FrontCatalogLayerView
{
    protected $_filterBlocks = null;
    
    public function getStateInfo()
    {
        $hlp = Mage::helper('adjnav');
        
        $ajaxUrl = '';
        if ($hlp->isSearch()){
            $ajaxUrl = Mage::getUrl('adjnav/ajax/search');
        }
        elseif ($cat = $this->getLayer()->getCurrentCategory()){
            $ajaxUrl = Mage::getUrl('adjnav/ajax/category', array('id'=>$cat->getId()));
        }
        $ajaxUrl = $hlp->stripQuery($ajaxUrl);
        
        //it could be search, home or category
        $url     = $hlp->getContinueShoppingUrl();
        
        $pageKey = Mage::getBlockSingleton('page/html_pager')->getPageVarName();
        $queryStr = $hlp->getParams(true, $pageKey);
        if ($queryStr)
            $queryStr = substr($queryStr,1);
       
        $this->setClearAllUrl($hlp->getClearAllUrl($url));

        if (false !== strpos($url, '?'))
        {
            $url = substr($url, 0, strpos($url, '?'));
        }
        return array($url, $queryStr, $ajaxUrl);
    }

    public function bNeedClearAll()
    {
        return Mage::helper('adjnav')->bNeedClearAll();
    }
    
    protected function _prepareLayout()
    {    	
    	// Notifies Magento Booster that the Layered Navigation is loaded    	
		Mage::register('adjustware_layered_navigation_view', true, true);
        
        //remove Enterprise_PageCache data
   	    Mage::app()->getCache()->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array(Enterprise_PageCache_Model_Processor::CACHE_TAG));
    
        // get current category ID
        
        $category = Mage::registry('current_category');
        
        if ($category)
        {
            $iCurrentCatId = $category->getId();
        }
        else 
        {
            $iCurrentCatId = null;
        }
        
        // get last cat ID
        
        $sessionObject = Mage::getSingleton('catalog/session');
        $request = Mage::app()->getRequest();

        if ($sessionObject && ($iLastCatId = $sessionObject->getAdjnavLastCategoryId()))
        {
            if (($iCurrentCatId != $iLastCatId) && !$request->isXmlHttpRequest())
            {
                Mage::register('adjnav_new_category', true);
            }
        }
        
        $sessionObject->setAdjnavLastCategoryId($iCurrentCatId);
        
        //preload setting    
        $this->setIsRemoveLinks(Mage::getStoreConfig('design/adjnav/remove_links'));
            
        //blocks    
        $this->createCategoriesBlock();

        $filterableAttributes = $this->_getFilterableAttributes();
        
        // we rewrite this piece of code
        // to make sure price filter is applied last
        $blocks = array();
        foreach ($filterableAttributes as $attribute) {
            #print_r($attribute);
            $blockType = 'adjnav/catalog_layer_filter_attribute';
            
            if ($attribute->getFrontendInput() == 'price') 
            {
                $blockType = 'adjnav/catalog_layer_filter_price';
            }
            
            $name = $attribute->getAttributeCode().'_filter';
            $blocks[$name] = $this->getLayout()->createBlock($blockType)
                ->setLayer($this->getLayer())
                ->setAttributeModel($attribute);
                    
            $this->setChild($name, $blocks[$name]);
        }
        
        foreach ($blocks as $name=>$block){
            #echo "<br>" . $name . "<br>";
            $block->init();
            if(Mage::helper('adjnav')->isSolrEnabled())
            {
            	$block->addFacetCondition();	
            }  
        }
        
        $this->getLayer()->apply();
        
        return Mage_Core_Block_Abstract::_prepareLayout();
    }  
    
    protected function createCategoriesBlock()
    {
        $categoryBlock = $this->getLayout()->createBlock('adjnav/catalog_layer_filter_category')
            ->setLayer($this->getLayer())
            ->init();
        if(Mage::helper('adjnav')->isSolrEnabled())
        {
        	$categoryBlock->addFacetCondition();	
        }   
        if ('none' != Mage::getStoreConfig('design/adjnav/cat_style'))
        { 
            $this->setChild('category_filter', $categoryBlock);
        }
    }
    
    public function getFilters()
    {
        if (is_null($this->_filterBlocks))
        {
            $this->_filterBlocks = parent::getFilters();
            /* @TODO Create Mage::dispatchEvent() here and create an observer in Visualize your attributes module */
            $val = Mage::getConfig()->getNode('modules/AdjustWare_Icon/active');
    	    if ((string)$val == 'true')
    	    {
    	        Mage::helper('adjicon')->addIconsToFilters($this->_filterBlocks);
    	    }
            Mage::dispatchEvent('adjustware_nav_layer_filters_get_after', array('layer_view_block' => $this));

    	    //$this->_rangeFilters();
        }	    
	    return $this->_filterBlocks;
    }
    
    protected function _toHtml(){
        $html = parent::_toHtml();  
        if (!Mage::app()->getRequest()->isXmlHttpRequest()){
            $html = '<div id="adj-nav-navigation">' . $html . '</div>';
        }
        return $html; 
    }
    
    /**
     *
     * @param string $name
     * @return AdjustWare_Nav_Block_Catalog_Layer_View 
     */
    public function unsetFilter($name)
    {
        unset($this->_filterBlocks[$name]);
        return $this;
    }

    /**
     * @author ksenevich@aitoc.com
     */
    protected function _rangeFilters()
    {
        $featuredLimit  = Mage::helper('adjnav/featured')->getFeaturedAttrsLimit();
        $featuredLimitDisabled = 0 == $featuredLimit;
        if (!$featuredLimit && !$featuredLimitDisabled)
        {
            return false;
        }

        $newFilterOrder = array();
        $attributes     = array();
        foreach ($this->_filterBlocks as $filter)
        {
            if ($filter instanceof AdjustWare_Nav_Block_Catalog_Layer_Filter_Attribute)
            {
                $attributes[$filter->getAttributeId()] = $filter;
            }
            else 
            {
                $newFilterOrder[] = $filter;
            }
        }

        $attributes = Mage::getModel('adjnav/eav_entity_attribute_stat')->rangeAttributes($attributes);

        if (Mage::helper('adjnav/featured')->isRangeAttributes())
        {
            $this->_filterBlocks = array_merge($newFilterOrder, $attributes);
        }
    }

    public function getAttributesCount()
    {
        $count = 0;
        foreach ($this->_filterBlocks as $filter)
        {
            if ($filter instanceof AdjustWare_Nav_Block_Catalog_Layer_Filter_Attribute)
            {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @author ksenevich@aitoc.com
     */
    public function isShowMoreAttributesButton()
    {
        $featuredLimit = Mage::helper('adjnav/featured')->getFeaturedAttrsLimit();

        return ($featuredLimit && $featuredLimit < $this->getAttributesCount());
    }
} } 