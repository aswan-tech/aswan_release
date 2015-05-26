<?php
/**
 * Product:     Layered Navigation Pro for Enterprise Edition - 16/08/12
 * Package:     AdjustWare_Nav_10.4.9_10.0.0_557110
 * Purchase ID: hXgQU3oI8FOfJ8PDCk5s6h6XPM5oH2Kx0N6TCAPkqN
 * Generated:   2013-04-22 06:59:44
 * File path:   app/code/local/AdjustWare/Nav/Block/Catalog/Layer/Filter/Price.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Nav')){ acZoMhqBerCjMkZa('d6bfa49e97b94a27367ee3419d9b2966'); ?><?php
class AdjustWare_Nav_Block_Catalog_Layer_Filter_Price extends Mage_Catalog_Block_Layer_Filter_Price
{
    private $_style;
    
    public function __construct()
    {
        parent::__construct();
        $this->_style = Mage::getStoreConfig('design/adjnav/price_style');
        $this->setTemplate('adjnav/filter_price_' . $this->_style . '.phtml');
        
        $this->_filterModelName = 'adjnav/catalog_layer_filter_price';
    }
    
    public function getVar(){
        return $this->_filter->getRequestVar();
    }
    
    public function getDelimeter() {
        if( version_compare( Mage::getVersion(),'1.12.0.0','>=' ) ) {
            return '-'; //magento 1.12+ "$from-$to"
        }
        return ',';//matento 1.11- "$index,$range"
    }

    public function getClearUrl()
    {
        $url = '';
        $query = Mage::helper('adjnav')->getParams();
//        if ('slider' != $this->_style && !empty($query[$this->getVar()])){
        if (!empty($query[$this->getVar()])){
            $query[$this->getVar()] = null;
            $url = Mage::getUrl('*/*/*', array(
                '_use_rewrite' => true, 
                '_query'       => $query,
            )); 
        }
        return $url;
    }
    
    public function isSelected($item)
    {
        return ($item->getValueString() == $this->_filter->getActiveState());        
    }
    
    public function getItemUrl($_item) 
    {
        $href = $this->htmlEscape($currentUrl = Mage::app()->getRequest()->getBaseUrl());

        //if (!$hideLinks)
        {
            $href .= $this->getRequestPath();
                
            $params = Mage::helper('adjnav')->getParams();
            $params[$this->getVar()] = $_item->getValueString();

            if ($params = http_build_query($params))
            {
                $href .= '?' . $params;
            }        
        }
        return $href;
    }
    
    /**
     * Will return GET part of the request
     *
     *    @return string
     */    
    public function getRequestPath()
    {
        $request = Mage::app()->getRequest();
        
        $requestPath = '';
        
        if ($request->isXmlHttpRequest())
        {
            $requestPath = Mage::getSingleton('core/session')->getRequestPath();
        }
        else
        {
            Mage::getSingleton('core/session')->setRequestPath($requestPath = $request->getRequestString());
        }
        
        return $this->htmlEscape($requestPath);
    }    
    
    public function getSymbol()
    {
        $s = $this->getData('symbol');
        if (!$s){
            $code = Mage::app()->getStore()->getCurrentCurrencyCode();
            $s = trim(Mage::app()->getLocale()->currency($code)->getSymbol());
            
            $this->setData('symbol', $s);
        }
        return $s;
    }
    
    /**
     * Add params to faceted search
     *
     * @return Enterprise_Search_Block_Catalog_Layer_Filter_Price
     */
    public function addFacetCondition()
    {
        $this->_filter->addFacetCondition();
        return $this;
    }

    public function getCollectionSize() {
        return $this->getLayer()->getProductCollection()->getSize();
    }
} } 