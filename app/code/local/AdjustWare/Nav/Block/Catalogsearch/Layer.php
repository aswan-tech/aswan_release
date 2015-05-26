<?php
/**
 * Product:     Layered Navigation Pro for Enterprise Edition - 16/08/12
 * Package:     AdjustWare_Nav_10.4.9_10.0.0_557110
 * Purchase ID: hXgQU3oI8FOfJ8PDCk5s6h6XPM5oH2Kx0N6TCAPkqN
 * Generated:   2013-04-22 06:59:44
 * File path:   app/code/local/AdjustWare/Nav/Block/Catalogsearch/Layer.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Nav')){ mREwjpoZaMTrjgEm('2ed961bf18391dc037c917a3ddbf89f8'); ?><?php
class AdjustWare_Nav_Block_Catalogsearch_Layer extends AdjustWare_Nav_Block_Rewrite_EnterpriseSearchCatalogsearchLayer//AdjustWare_Nav_Block_Catalog_Layer_View   
{

//    public function getLayer()
//    {
        //return Mage::getSingleton('catalogsearch/layer');
//        return parent::getLayer();
//    }

    /**
     * Check availability display layer block
     *
     * @return bool
     */
    public function canShowBlock()
    {
        $availableResCount = (int) Mage::app()->getStore()
            ->getConfig(Mage_CatalogSearch_Model_Layer::XML_PATH_DISPLAY_LAYER_COUNT );

        if (!$availableResCount
            || ($availableResCount>=$this->getLayer()->getProductCollection()->getSize())) {
            return parent::canShowBlock();
        }      
        return false;
    }
    
    protected function _prepareLayout()
    {   
        // Notifies Magento Booster that the Layered Navigation is loaded        
        Mage::register('adjustware_layered_navigation_view', true, true);
        
        //remove Enterprise_PageCache data
        Mage::app()->getCache()->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array(Enterprise_PageCache_Model_Processor::CACHE_TAG));
        
        //preload setting    
        $this->setIsRemoveLinks(Mage::getStoreConfig('design/adjnav/remove_links'));
            
        //blocks    
        $this->createCategoriesBlock();

        $filterableAttributes = $this->_getFilterableAttributes();
        
        // we rewrite this piece of code
        // to make sure price filter is applied last
        $blocks = array();
        foreach ($filterableAttributes as $attribute) {
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
                $block  ->init();
                if(Mage::helper('adjnav')->isSolrEnabled())
                {
                	$block->addFacetCondition();	
                }        
        }
        
        $this->getLayer()->apply();
        
        return $this;   
    }   
    
    protected function createCategoriesBlock(){
        $categoryBlock = $this->getLayout()
            ->createBlock('adjnav/catalog_layer_filter_categorysearch')
            ->setLayer($this->getLayer())
            ->init();
        if(Mage::helper('adjnav')->isSolrEnabled())
        {
        	$categoryBlock->addFacetCondition();	
        } 
        $this->setChild('category_filter', $categoryBlock);
    }
    
    protected function _toHtml(){
        $html = parent::_toHtml();  
        if (!Mage::app()->getRequest()->isXmlHttpRequest()){
            $html = '<div id="adj-nav-navigation">' . $html . '</div>';
        }
        return $html; 
    }
    
    public function bNeedClearAll()
    {
        return Mage::helper('adjnav')->bNeedClearAll();
    }
    
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

    public function getAttributesCount()
    {
        $count = 0;
        foreach ($this->_filterBlocks as $filter)
        {
            if ($filter instanceof AdjustWare_Nav_Block_Catalog_Layer_Filter_Attribute && $filter->getItemsCount())
            {
                $count++;
            }
        }

        return $count;
    }

    public function isShowMoreAttributesButton()
    {
        $featuredLimit = Mage::helper('adjnav/featured')->getFeaturedAttrsLimit();

        return ($featuredLimit && $featuredLimit < $this->getAttributesCount());
    }
} } 