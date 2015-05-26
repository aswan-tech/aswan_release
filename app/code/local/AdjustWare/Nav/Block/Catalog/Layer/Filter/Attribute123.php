<?php
/**
 * Product:     Layered Navigation Pro for Enterprise Edition - 16/08/12
 * Package:     AdjustWare_Nav_10.4.9_10.0.0_557110
 * Purchase ID: hXgQU3oI8FOfJ8PDCk5s6h6XPM5oH2Kx0N6TCAPkqN
 * Generated:   2013-04-22 06:59:44
 * File path:   app/code/local/AdjustWare/Nav/Block/Catalog/Layer/Filter/Attribute.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Nav')){ rIapgwhejkcDgyar('bbc2e7cf6e66777b11c0edc86f5a5e3f'); ?><?php
class AdjustWare_Nav_Block_Catalog_Layer_Filter_Attribute extends Mage_Catalog_Block_Layer_Filter_Attribute
{
    protected $_featuredItems = array();
    protected $_optionUses    = array();

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('adjnav/filter_attribute.phtml');
        $this->_filterModelName = 'adjnav/catalog_layer_filter_attribute';
    }
    
    public function getVar(){
        return $this->_filter->getRequestVar();
    }
    
    public function getClearUrl()
    {
        $url = '';
        $query = Mage::helper('adjnav')->getParams();
        if (!empty($query[$this->getVar()])){
            $query[$this->getVar()] = null;
            $url = Mage::getUrl('*/*/*', array(
                '_use_rewrite' => true, 
                '_query'       => $query,
             )); 
        }
        
        return $url;
    }
    
    public function getHtmlId($item)
    {
        return $this->getVar() . '-' . $item->getValueString();        
    }
    
    public function isSelected($item)
    {
        $ids = (array)$this->_filter->getActiveState();
        return in_array($item->getValueString(), $ids);        
    }
    //compatibility with solr
    public function addFacetCondition()
    {
        $this->_filter->addFacetCondition();
        return $this;
    }
    
    public function getItemsArray()
    {   
        $items                = array(); 
        $this->_featuredItems = array();
        $featuredValuesLimitDisabled = false;
        $featuredValuesLimit  = $this->helper('adjnav/featured')->getFeaturedValuesLimit();
        if($featuredValuesLimit == 0) {
            $featuredValuesLimitDisabled = true;
        }
        $iconsOnly            = (3 == $this->getColumnsNum()); 
        $baseUrl              = Mage::app()->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA); 
        $hideLinks            = Mage::getStoreConfig('design/adjnav/remove_links');
        foreach ($this->getItems() as $_item)
        {
			$htmlParams = 'id="' . $this->getHtmlId($_item) . '" ';
			
			$href = $this->htmlEscape($currentUrl = Mage::app()->getRequest()->getBaseUrl());

			if (!$hideLinks)
			{
				$href .= $this->getRequestPath();
				
				$params = Mage::helper('adjnav')->getParams();
				
				if (isset($params[$this->getVar()]))
				{
					$values = explode('-', $params[$this->getVar()]);	            		            	
                    $valueKey = array_search($_item->getValueString(), $values);
                    if (false === $valueKey)
                    {
                        $values[] = $_item->getValueString();
                    }
                    else
                    {
                        unset($values[$valueKey]);
                    }
					$params[$this->getVar()] = implode('-', array_unique($values));	            	
				}
				else
				{
					$params[$this->getVar()] = $_item->getValueString();
				}				
				
				if ($params = http_build_query($params))
				{
					$href .= '?' . $params;
				}		
			}
			
			$htmlParams .= 'href="' . $href . '" ';
                
            if ($iconsOnly){
                $htmlParams .= ' title="'.$this->htmlEscape($_item->getLabel()).'" class="adj-nav-icon ' 
                            . ($this->isSelected($_item) ? 'adj-nav-icon-selected' : '') . '" ';
            }
            else{
                $htmlParams .= 'class="adj-nav-attribute ' 
                            . ($featuredValuesLimitDisabled || $featuredValuesLimit > 0 ? '' : 'other ' )
                            . ($this->isSelected($_item) ? 'adj-nav-attribute-selected' : '') . '" ';
            }
            
            $icon = '';
            if ($_item->getIcon()){
                $icon = '<img border="0" alt="'.$this->htmlEscape($_item->getLabel()).'" src="'.$baseUrl.'icons/'.$_item->getIcon().'" />';
            } 
        
            $qty = '';
            if (!$this->getHideQty()) 
                //$qty =  '(' .  $_item->getCount() .')';
        
            $label = $_item->getLabel();
            if ($iconsOnly){
                $label = '';
            }
            $label = $icon . $label;
            
            $items[] = '<a onclick="return false;" '.$htmlParams.'>'.$label.'</a>'.$qty;
            $isFeaturedItem = false;
            if (($featuredValuesLimit > 0) || $featuredValuesLimitDisabled)
            {
                $isFeaturedItem = true;
                if (!$featuredValuesLimitDisabled)
                {
                    $featuredValuesLimit--;
                }
            }
            $this->_featuredItems[] = $isFeaturedItem;
        }
        
        return $items;
    }
    
    /**
     * Will return GET part of the request
     *
     *	@return string
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

    public function getFeaturedItemStyle($key)
    {
        if (!empty($this->_featuredItems[$key]))
        {
            return 'attr-val-featured';
        }

        return 'attr-val-other';
    }

    public function isShowMoreButton()
    {
        $featuredValuesLimit = $this->helper('adjnav/featured')->getFeaturedValuesLimit();
        if ($featuredValuesLimit && $featuredValuesLimit < count($this->getItems()))
        {
            return true;
        }

        return false;
    }

    /** Implement custom sorting for items if configured
     * 
     * @see Mage_Catalog_Model_Layer_Filter_Abstract::getItems()
     * @author ksenevich@aitoc.com
     */
    public function getItems()
    {
        $items = parent::getItems();

        $featuredLimit = Mage::helper('adjnav/featured')->getFeaturedValuesLimit();
        $featuredLimitDisabled = $featuredLimit == 0;
        if (!Mage::helper('adjnav/featured')->isRangeValues())
        {
            return $items;
        }

        $usesRanges  = array();
        $names       = array();
        $attributeId = $this->getAttributeModel()->getId();
        $optionUses  = Mage::getModel('adjnav/eav_entity_attribute_option_stat')->getSortedOptions($attributeId);

        foreach ($items as $k => $item)
        {
            $item->setSortRange(0);

            if (isset($optionUses[$item->getValueString()]))
            {
                $item->setSortRange($optionUses[$item->getValueString()]);
            }
        }

        usort($items, array($this, 'sortItems'));

        $featuredIndex = array();
        $names         = array();
        foreach ($items as $k => $item)
        {
            $item->setSortRange(0);

            if (($k < $featuredLimit) || $featuredLimitDisabled)
            {
                if ($featuredLimitDisabled)
                {
                    $item->setSortRange(1000000 - $k);
                }
                else
                {
                    $item->setSortRange($featuredLimit - $k);
                }
            }
        }

        usort($items, array($this, 'sortItems'));

        return $items;
    }

    public function getAttributeId()
    {
        return $this->_filter->getAttributeModel()->getId();
    }

    public function sortItems($item1, $item2)
    {
        if ($item1->getSortRange() == $item2->getSortRange())
        {//Zend_Debug::dump($item1->getLabel().' '.$item2->getLabel());
            return strcmp($item1->getLabel(), $item2->getLabel());
        }

        return (($item1->getSortRange() < $item2->getSortRange()) ? 1 : -1);
    }
} } 