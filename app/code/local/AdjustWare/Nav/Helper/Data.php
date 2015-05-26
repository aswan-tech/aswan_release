<?php
/**
 * Product:     Layered Navigation Pro for Enterprise Edition - 16/08/12
 * Package:     AdjustWare_Nav_10.4.9_10.0.0_557110
 * Purchase ID: hXgQU3oI8FOfJ8PDCk5s6h6XPM5oH2Kx0N6TCAPkqN
 * Generated:   2013-04-22 06:59:44
 * File path:   app/code/local/AdjustWare/Nav/Helper/Data.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Nav')){ rIapgwhejkcDgyar('9e7153314d37394fedb2b8cb6ea9f7ab'); ?><?php
class AdjustWare_Nav_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_params = null;
    protected $_continueShoppingUrl = null;
    protected $_module='catalog';
    protected $_solrModel = 'enterprise_search';
    protected $_solrEnabled = null;
    
    CONST CATEGORY_STYLE = 'design/adjnav/cat_style';
        
    public function isSearch()
    {
        $mod = Mage::app()->getRequest()->getModuleName();
        if ('catalogsearch' === $mod)
        {
            return true;
        }
            
        if ('adjnav' === $mod && 'search' == Mage::app()->getRequest()->getActionName())
        {
            return true;
        }
        
        return false;
    }
    
    public function getCategoryStyle()
    {
        return Mage::getStoreConfig(self::CATEGORY_STYLE);
    }
    
    public function isCategoryStyleNone()
    {
        return ($this->getCategoryStyle() == 'none') ? true : false;
    }
    
    public function isCategoryCleared( $checkAdjClear = false )
    {
        $request = Mage::app()->getRequest();
        if( $request->getQuery('cat') == 'clear' ) 
        {
            return true;
        }
        if($checkAdjClear && $request->getParam('adjclear', false))
        {
            return true;
        }
        return false;
    }
    
    public function getContinueShoppingUrl()
    {
        if (is_null($this->_continueShoppingUrl))
        {
            $url = '';
            
            $allParams = $this->getParams();
            $keys = $this->getNonFilteringParamKeys();
            
            $query = array();
            foreach ($allParams as $k=>$v){
                if (in_array($k, $keys))
                    $query[$k] = $v;
            }
            
            if ($this->isSearch()){
                $url = Mage::getModel('core/url')->getUrl('catalogsearch/result/index', array('_query'=>$query));
            }
            else {
                $category = Mage::registry('current_category');
                $rootId = Mage::app()->getStore()->getRootCategoryId();
                if ($category && $category->getId() != $rootId){
                    $url = $category->getUrl();
                }
                else {
                    $url = Mage::app()->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
                }
                $url .= $this->toQuery($query);
            } 
            $this->_continueShoppingUrl = $url;      
        }
        
        return $this->_continueShoppingUrl;
    }
    
    public function wrapProducts($html)
    {
        if ($this->mayUseLayeredNavigation())
        {
            $html = str_replace('onchange="setLocation', 'onchange="adj_nav_toolbar_make_request', $html);
        }  
        $loaderHtml =  '<div class="adj-nav-progress" style="display:none"><img src="'. Mage::getDesign()->getSkinUrl('images-v3/adj-nav-progress-v1.gif') .'" /></div>';  
        $html .= $loaderHtml;
        
        if (Mage::app()->getRequest()->isXmlHttpRequest()){
            $html = str_replace('?___SID=U&amp;', '?', $html);
            $html = str_replace('?___SID=U', '', $html);
            $html = str_replace('&amp;___SID=U', '', $html);
            
            $k = Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED;
            $v = Mage::helper('core')->urlEncode($this->getContinueShoppingUrl());
            $html = preg_replace("#$k/[^/]+#","$k/$v", $html);
            
        }
        else {
            $html = '<div id="adj-nav-container">'
                  . $html
                  . '</div>'
                  . '';
        }    

	if (Mage::getStoreConfig('design/adjnav/scroll_to_top'))
	{
            $html .= '<script>
                        scroll(0,0);
                      </script>';
        }

        return $html;        
    }
    
    public function getLayerModelName()
    {
        $this->_module = ($this->isSearch()) ? 'catalogsearch' : 'catalog';
        $moduleName = $this->_module . '/layer';
        if($this->isSolrEnabled())
        {
            $moduleName = ($this->isSearch()) ? $this->_solrModel . '/search_layer' : $this->_solrModel . '/catalog_layer';     
        }
        return  (string)$moduleName;
    }
    

    public function isSolrEnabled()
    {
        if(is_null($this->_solrEnabled))
        {
            $helper = Mage::helper('enterprise_search');
            $this->_solrEnabled = ($helper->isThirdPartSearchEngine() && $helper->getIsEngineAvailableForNavigation());
        }
        return $this->_solrEnabled;
    } 

    public function wrapHomepage($html)
    {
        $loaderHtml =  '<div class="adj-nav-progress" style="display:none"><img src="'. Mage::getDesign()->getSkinUrl('images/adj-nav-progress-v1.gif') .'" /></div>';  

        $html = '<div id="adj-nav-container">'
              . $html
              . $loaderHtml
              . '</div>'
              . '<script>adj_nav_toolbar_init()</script>';
        return $html;       
    }
    
    
    public function getParam($k){
        $p = $this->getParams();
        $v = isset($p[$k]) ? $p[$k] : null;
        return $v;
    }
    
    // currently we use $without only if $asString=true
    public function getParams($asString=false, $without=null)
    {
        if (is_null($this->_params)){			
            $sessionObject = Mage::getSingleton('catalog/session');

            $bNeedClearAll = false;
            $bPreserveCategoryFilter = false;
            
            if (Mage::getStoreConfig('design/adjnav/reset_filters') AND Mage::registry('adjnav_new_category'))
            {
                $bNeedClearAll = true;
                $bPreserveCategoryFilter = true;
            }
            
            if ($this->isSearch())
            {
                $sessionObject = Mage::getSingleton('catalogsearch/session');
                $query = Mage::app()->getRequest()->getQuery();
                if (isset($query['q']))
                {
                    if ($sessionObject->getData('advnavquery') && $sessionObject->getData('advnavquery') != $query['q'])
                    {
                        $bNeedClearAll = true;
                    }
                    $sessionObject->setData('advnavquery', $query['q']);
                }
            }

            // start fix for diff currency and input type
            
            $nSavedCurrencyRate = $sessionObject->getAdjNavCurrencyRate();
            
            $nCurrentCurrencyRate =  Mage::app()->getStore()->convertPrice(1000000, false);
            $nCurrentCurrencyRate = $nCurrentCurrencyRate / 1000000;
            
            $nSavedPriceStyle = $sessionObject->getAdjNavPriceStyle();
            $nCurrentPriceStyle = Mage::getStoreConfig('design/adjnav/price_style');
            
            $bNeedClearPriceFilter = false;
            
            if ($nSavedCurrencyRate AND $nSavedCurrencyRate != $nCurrentCurrencyRate)
            {
                $bNeedClearPriceFilter = true;
            }
            
            if ($nSavedPriceStyle != $nCurrentPriceStyle)
            {
                $bNeedClearPriceFilter = true;
            }
            
            if ($bNeedClearPriceFilter)
            {
                $sess  = (array)$sessionObject->getAdjNav();
                
                if ($sess)
                {
                    $aNonFilteringParamKeys = $this->getNonFilteringParamKeys();
                    
                    foreach ($sess as $sKey => $sVal)
                    {
                        if (!in_array($sKey, $aNonFilteringParamKeys))
                        {
                            $attribute = Mage::getModel('eav/entity_attribute');

                            $attribute->load($sKey, 'attribute_code');
                            
                            if ($attribute->getFrontendInput() == 'price')
                            {
                                unset($sess[$sKey]);
                            }
                        }
                    }
                    
                    $sessionObject->setAdjNav($sess);
                }
            }
            
            $sessionObject->setAdjNavCurrencyRate($nCurrentCurrencyRate);
            $sessionObject->setAdjNavPriceStyle($nCurrentPriceStyle);
            
            // end fix for diff currency and stores
            
            
            $query = Mage::app()->getRequest()->getQuery();
            $sess  = (array)$sessionObject->getAdjNav();
            $sess  = array(); // @author ksenevich@aitoc.com Disable session storage of params with ajax hashes implementation
            $this->_params = array_merge($sess, $query);
            
            if (!empty($query['adjclear']) OR $bNeedClearAll)
            {
                $back = $this->_params;
                $this->_params = array();
                if ($bPreserveCategoryFilter && isset($back['cat']) && is_numeric($back['cat']))
                {
                    //checking if category was changed and if it wasn't 'clear'ed
                    $this->_params['cat'] = $back['cat'];
                }                
                if ($this->isSearch() && isset($query['q']))
                {
                    $this->_params['q'] = $query['q'];
                }
                unset($back);
            }
            //remove empty
            $sess = array();
            foreach ($this->_params as $k => $v){
                if ($v && 'clear' != $v)
                    $sess[$k] = $v;
            }
            
            if (Mage::registry('adjnav_new_category') AND isset($sess['p']))
            {
                unset($sess['p']);
            }
            
            $sessionObject->setAdjNav($sess);
            $this->_params = $sess;
            
            Mage::register('adjnav_current_session_params', $sess);
            
            // add values from session to request for product list toolbar
            // this code assumes we call the function BEFORE toolbar,
            // in general it is not correct
            foreach ($this->getNonFilteringParamKeys() as $k){
                if (!empty($sess[$k])){
                   # Mage::app()->getRequest()->setParam($k, $sess[$k]);     <-- this string add to url $_GET params like join("/",$_GET)
                }
            }

            Mage::dispatchEvent('adjustware_nav_layer_set_params_after', array(
                'helper' =>  $this,
            ));
        }
		
		if ($asString){
            return $this->toQuery($this->_params, $without);
        }
        
        return $this->_params;
    }

    /**
     *
     * @param string $key
     * @param mixed $value
     * @return AdjustWare_Nav_Helper_Data 
     */
    public function setParam($key, $value = null)
    {        
        $this->_params[$key] = $value;
        return $this;
    }
    
    /**
     *
     * @param string $key
     * @return AdjustWare_Nav_Helper_Data 
     */
    public function unsetParam($key)
    {        
        unset($this->_params[$key]);        
        return $this;
    }
    
    public function toQuery($params, $without=null)
    {           
        if (!is_array($without))
            $without = array($without);
            
        $queryStr = '?';
        foreach ($params as $k => $v){
            if (!in_array($k, $without))
                $queryStr .= $k . '=' . urlencode($v) . '&';    
        }
        return substr($queryStr, 0, -1);           
    }
    
    public function stripQuery($url)
    {
        $pos = strpos($url, '?');
        if (false !== $pos)
            $url = substr($url, 0, $pos);
            
        return $url;
    }
    
    public function getClearAllUrl($baseUrl)
    {
        $baseUrl .= '?adjclear=true';
        if ($this->isSearch())
            $baseUrl .= '&q=' . urlencode($this->getParam('q'));  
               
        return $baseUrl;
    }
    
    public function bNeedClearAll()
    {
        if ($aParams = Mage::registry('adjnav_current_session_params'))
        {
            $bNeedClearAll = false;
            
            $aNonFilteringParamKeys = $this->getNonFilteringParamKeys();
            
            foreach ($aParams as $sKey => $sVal)
            {
                if (!in_array($sKey, $aNonFilteringParamKeys))
                {
                    $bNeedClearAll = true;
                }
            }
			// added by dhananjay
			if(isset($aParams['lookCatId']))
			     $bNeedClearAll = false;
            
            return $bNeedClearAll;
        }
        else 
        {
            return false;
        }
        
        return true;
    }
    
    public function getCacheKey($attrCode){
        $keys = $this->getNonFilteringParamKeys();
        $keys[] = $attrCode;        
        return md5($this->getParams(true, $keys) . $attrCode);
    }
    
    protected function getNonFilteringParamKeys(){
        return array('x','y','mode','p','order','dir','limit','q','___store', '___from_store','sns','no_cache');
    }
    
    public function getFilteringParams() 
    {
        return $this->getParams(true, $this->getNonFilteringParamKeys());    
    }

    public function isPageAutoload()
    {
        if (!$this->mayUseLayeredNavigation())
        {
            return false;
        }
        $category = Mage::registry('current_category');
        
        $isAutoloadEnabled = Mage::getStoreConfig('design/adjnav_endless_page/enable_page_autoload');
        
        if ($category)
        {
            if ($category->getIsAnchor())
            {
                return $isAutoloadEnabled;
            } else {
                if ($category->getId() == Mage::app()->getStore()->getRootCategoryId())
                {
                    return $isAutoloadEnabled;
                }
            }
        } else {
            return $isAutoloadEnabled;
        }
    }
    
    public function mayUseLayeredNavigation()
    {
        if($this->isModuleEnabled('Aitoc_Aitmanufacturers'))
        {
            $canUseLNP = false;
            if(version_compare(Mage::getVersion(), '1.4.0.0', '>='))
                $canUseLNP = Mage::helper('aitmanufacturers')->canUseLayeredNavigation(Mage::registry('shopby_attribute'), true);
            else
                $canUseLNP = Mage::helper('aitmanufacturers')->canUseLayeredNavigation();
                
            if($canUseLNP)
                return true;
        }
        
        $category = Mage::registry('current_category');
        if ($category)
        {
            $pageLayout = $this->getPageLayout($category);
            
            if ($category->getIsAnchor() &&
                in_array($pageLayout, array('', 'two_columns_left', 'three_columns')))
            {
                return true;
            }
        } else {
            if ($this->isSearch())
            {
                return true;
            }
        }
    }
    
    public function getPageLayout($category)
    {
        if (Aitoc_Aitsys_Abstract_Service::get()->isMagentoVersion('>=1.10'))
        {
            $design = Mage::getSingleton('catalog/design');
            $settings = $design->getDesignSettings($category);
            $pageLayout = $settings->getPageLayout();
        } else {
            $pageLayout = $category->getPageLayout();
        }
        return $pageLayout;
    }
    
    public function isModuleEnabled($moduleName = null)
    {
        if ($moduleName === null) {
            $moduleName = $this->_getModuleName();
        }

        if (!Mage::getConfig()->getNode('modules/' . $moduleName)) {
            return false;
        }

        $isActive = Mage::getConfig()->getNode('modules/' . $moduleName . '/active');
        if (!$isActive || !in_array((string)$isActive, array('true', '1'))) {
            return false;
        }
        return true;
    }
    
    /**
     * 
     * @return Varien_Object
     */
    public function getShopByBrandsStatus()
    {        
        $aModuleList = Mage::getModel('aitsys/aitsys')->getAitocModuleList();
        
        $data = array(
            'is_installed' => false,
            'is_enabled' => false,
        );
        
        $aModuleList = Mage::getModel('aitsys/aitsys')->getAitocModuleList();
        
        if ($aModuleList)
        {
            foreach ($aModuleList as $module)
            {                
                if ('Aitoc_Aitmanufacturers' == $module['key'])
                {
                    $data = array(
                        'is_installed' => (bool) $module->isAvailable(),
                        'is_enabled' => (bool) $module['value'],
                    );
                }
            }
        }

        return new Varien_Object($data);
    }    
} } 
