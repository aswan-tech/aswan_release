<?php
/**
 * Product:     Layered Navigation Pro for Enterprise Edition - 16/08/12
 * Package:     AdjustWare_Nav_10.4.9_10.0.0_557110
 * Purchase ID: hXgQU3oI8FOfJ8PDCk5s6h6XPM5oH2Kx0N6TCAPkqN
 * Generated:   2013-04-22 06:59:44
 * File path:   app/code/local/AdjustWare/Nav/Model/Catalog/Layer/Filter/Attribute.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Nav')){ MQPiDqpargRkDBPM('de30395b9b1d4b6f27155e733f9e638c'); ?><?php
class AdjustWare_Nav_Model_Catalog_Layer_Filter_Attribute extends Mage_Catalog_Model_Layer_Filter_Attribute
{
    private static $_filterAttributes = array();
    private static $_filterProducts   = array();

    public function __construct()
    {
        parent::__construct();
    }

    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $filter = Mage::helper('adjnav')->getParam($this->_requestVar);
        $filter = explode('-', $filter);
        
        $ids = array();    
        foreach ($filter as $id){
            $id = intVal($id);
            if ($id)
            {
                $ids[] = $id;
            }
                    
        } 
		
        if ($ids){
            $this->applyMultipleValuesFilter($ids);   
        }
		
        //compatibility with SOLR
        if(Mage::helper('adjnav')->isSolrEnabled())
        {
        	$this->applySolrFilter($ids);  	
        }
        //compatibility with SOLR         


        // Increment attribute usage statistics data
        if (count($ids))
        {
            Mage::getModel('adjnav/eav_entity_attribute_option_stat')->addStat($ids);
        }

        $this->setActiveState($ids);
        return $this;
    }
	
    public function applySolrFilter(array $ids)
    {
        if(empty($ids))
        {
            return $this;
        }
        foreach($ids as $id)
        {
             $value[] = $this->getAttributeOptionValue($id);   
        }
        $this->applyFilterToCollection($this,$value);
        $this->getLayer()->getState()->addFilter($this->_createItem($value, $value)); 
    }
    
    /*
        $id - attribute option_id
    */
    
    public function getAttributeOptionValue($ids)
    {
        $collection =   Mage::getModel('eav/entity_attribute_option')->getCollection()
                        ->addFieldToFilter('main_table.option_id', array('in' => $ids))
                        ->setStoreFilter();
        $optionArr = $collection->toOptionArray();
        return (count($optionArr) > 1) ? $optionArr : $optionArr[0]['label'];
    }
 
    // copied from catalogindex
    protected function applyMultipleValuesFilter($ids)
    {
        $collection = $this->getLayer()->getProductCollection();
        
        $attribute  = $this->getAttributeModel();
        $table = Mage::getSingleton('core/resource')->getTableName('catalogindex/eav'); //check for prefix
        $helper = Mage::helper('adjnav');
        
        $alias = 'attr_index_'.$attribute->getId();
        $collection->getSelect()
            ->join(array($alias => $table), $alias.'.entity_id=e.entity_id', array())
     	    ->where($alias.'.store_id = ?', Mage::app()->getStore()->getId())
            ->where($alias.'.attribute_id = ?', $attribute->getId())
		    ->where($alias.'.value IN (?)', $ids);

		if (is_array($ids) && ($size = count($ids)))
		{
			$adapter = $collection->getConnection();
			$subQuery = new Varien_Db_Select($adapter);
			$subQuery
                ->from(array('e' => Mage::getModel('catalog/product')->getResource()->getTable('catalog/product')), 'entity_id')
                ->join(array('a' => Mage::getModel('catalog/product')->getResource()->getTable('catalog/product_index_eav')), 'a.entity_id = e.entity_id', array())
                ->where('a.store_id = ?', Mage::app()->getStore()->getId())
                ->where('a.attribute_id = ?', $attribute->getId())
                ->where('a.value IN (?)', $ids)
                ->group(array('a.entity_id', 'a.attribute_id', 'a.store_id'));

            /*if ('AND' == Mage::getStoreConfig('design/adjnav/filtering_logic'))
            {
                $subQuery->having($size.' = COUNT(a.value)');
            }*/

            /**
             * @author ksenevich@aitoc.com
             */
            if (Mage::helper('adjnav/version')->hasConfigurableFix())
            {
                $SBBStatus = $helper->getShopByBrandsStatus();
                $forbidConfigurables = $SBBStatus->getIsInstalled()
                    && $SBBStatus->getIsEnabled()
                    && Mage::helper('aitmanufacturers')->canUseLayeredNavigation(Mage::registry('shopby_attribute'), true);
                
                if (!$forbidConfigurables)
                {
                    $subQuery->where('e.type_id != ?', Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE);
                }
            }

			$res = $adapter->fetchCol($subQuery);

            /**
             * @author ksenevich@aitoc.com
             */
            self::_addFilterValues($attribute->getId(), $res, $ids);
		}
        
        if (count($ids)>1){
            $collection->getSelect()->distinct(true); 
        }
        
        return $this;
    }   
    
    /**
     * Retrieve layer object
     *
     * @return Mage_Catalog_Model_Layer
     */
    public function getLayer()
    {
        $layer = $this->_getData('layer');
        if (is_null($layer)) {
            $layer = Mage::getSingleton(Mage::helper('adjnav')->getLayerModelName());
            $this->setData('layer', $layer);
        }
        return $layer;
    }
    
    
    protected function _getOptionCountName(array $option)
    {
	    if(version_compare(Mage::getVersion(),'1.12.0.0','>=' ))
            return Mage::helper('adjnav')->isSolrEnabled() ? $option['value'] : $option['value'];    
		else
        return Mage::helper('adjnav')->isSolrEnabled() ? $option['label'] : $option['value'];    
    }
    
    /**
     * Get data array for building attribute filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $attribute = $this->getAttributeModel();
        $this->_requestVar = $attribute->getAttributeCode();

        $key = $this->getLayer()->getStateKey();
        $key .= Mage::helper('adjnav')->getCacheKey($this->_requestVar);
		
		$data = $this->getLayer()->getAggregator()->getCacheData($key);      		
		
		if ($data === null) {
            $data = array();
            
            $options = $attribute->getFrontend()->getSelectOptions();
            
            $optionsCount = Mage::getSingleton('catalogindex/attribute')->getCount(
                $attribute,
                $this->_getBaseCollectionSql()
            );
            //SOLR COMPATIBILITY . if use ajax faceted data didn't exists at step of creating layered
            if(Mage::helper('adjnav')->isSolrEnabled())
            {
			    if(version_compare(Mage::getVersion(),'1.12.0.0','>=' ))
		        {
		            $engine = Mage::getResourceSingleton('enterprise_search/engine');
                    $facetField = $engine->getSearchEngineFieldName($this->getAttributeModel(), 'nav');
                    $facetedData = $this->getLayer()->getProductCollection()->getFacetedData($facetField);
					$options = $attribute->getSource()->getAllOptions(false);
					if(!empty($facetedData))
                    {
                        $optionsCount = $facetedData;   
                    }
		        }
				else
				{
                $fieldName = Mage::helper('enterprise_search')->getAttributeSolrFieldName($attribute);
                $facetedData = $this->getLayer()->getProductCollection()->getFacetedData($fieldName);
                if(!empty($facetedData))
                {
                     $optionsCount = $facetedData;   
                }
            }   
            }   
			
            //SOLR COMPATIBILITY           
            foreach ($options as $option) {
                if(version_compare(Mage::getVersion(),'1.12.0.0','>=' ))
				{
				    if (!Mage::helper('core/string')->strlen($option['value']) || is_array($option['value']))
                        continue;
						
				    $optionId = $option['value'];
                    // Check filter type
                    if ($attribute->getIsFilterable() != self::OPTIONS_ONLY_WITH_RESULTS
                        || !empty($optionsCount[$this->_getOptionCountName($option)])
                        ) {
						$data[] = array(
                            'label' => $option['label'],
                            'value' => $option['value'],
                            'count' => isset($optionsCount[$this->_getOptionCountName($option)]) ? $optionsCount[$this->_getOptionCountName($option)] : 0,
                            );
                          }
			    }
                else
				{
			
                if (is_array($option['value'])) {
                    continue;
                }
                if (Mage::helper('core/string')->strlen($option['value'])) {
                    // Check filter type
                    if ($attribute->getIsFilterable() == self::OPTIONS_ONLY_WITH_RESULTS) {
                        if (!empty($optionsCount[$this->_getOptionCountName($option)])) {
                            $data[] = array(
                                'label' => $option['label'],
                                'value' => $option['value'],
                                'count' => $optionsCount[$this->_getOptionCountName($option)],
                            );
                        }
                    }
                    else {
                        $data[] = array(
                            'label' => $option['label'],
                            'value' => $option['value'],
                            'count' => isset($optionsCount[$this->_getOptionCountName($option)]) ? $optionsCount[$this->_getOptionCountName($option)] : 0,
                        );
                    }
                }
            }
            }

            $currentIds = Mage::helper('adjnav')->getParam($attribute->getAttributeCode());
            $tags = array(
                Mage_Eav_Model_Entity_Attribute::CACHE_TAG . ':' . $currentIds,
            );

            $tags = $this->getLayer()->getStateTags($tags);
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }
        return $data;
    }
    
    protected function _getBaseCollectionSql()
    {
        $alias = 'attr_index_' . $this->getAttributeModel()->getId(); 
        // Varien_Db_Select
        $baseSelect = clone parent::_getBaseCollectionSql();
        #echo "parent collection--" .  $baseSelect->__toString();
        # 1) remove from conditions
        $oldWhere = $baseSelect->getPart(Varien_Db_Select::WHERE);
        $newWhere = array();

        foreach ($oldWhere as $cond){
           if (!strpos($cond, $alias))
               $newWhere[] = $cond;
        }
  
        if ($newWhere && substr($newWhere[0], 0, 3) == 'AND')
           $newWhere[0] = substr($newWhere[0],3);        
        
        $baseSelect->setPart(Varien_Db_Select::WHERE, $newWhere);
        
        // 2) remove from joins
        $oldFrom = $baseSelect->getPart(Varien_Db_Select::FROM);
        $newFrom = $oldFrom;

        if (isset($newFrom[$alias]))
        {
            unset($newFrom[$alias]);
        }
        //it assumes we have at least one table 
        $baseSelect->setPart(Varien_Db_Select::FROM, $newFrom);  

        return $baseSelect;
    }
    
    protected function _getResource()
    {
        if (is_null($this->_resource)) {
            $this->_resource = Mage::getResourceModel('catalog/layer_filter_attribute');
        }
        return $this->_resource;
    }
    
    
    public function addFacetCondition()
    {
        if(version_compare(Mage::getVersion(),'1.12.0.0','>=' ))
		{
		    $engine = Mage::getResourceSingleton('enterprise_search/engine');
            $facetField = $engine->getSearchEngineFieldName($this->getAttributeModel(), 'nav');
            $this->getLayer()->getProductCollection()->setFacetCondition($facetField);
		}
		else
		{
        $facetField = Mage::helper('enterprise_search')->getAttributeSolrFieldName($this->getAttributeModel());
        $this->getLayer()->getProductCollection()->setFacetCondition($facetField);
		}

        return $this;
    }
    
    public function applyFilterToCollection($filter, $value)
    {
	    if(version_compare(Mage::getVersion(),'1.12.0.0','>=' ))
		{
	        if (empty($value) || (isset($value['from']) && empty($value['from']) && isset($value['to'])
                && empty($value['to']))
            ) {
                $value = array();
            }

            if (!is_array($value)) {
                $value = array($value);
            }


            $attribute = $filter->getAttributeModel();
            $options = $attribute->getSource()->getAllOptions();
            foreach ($value as &$valueText) {
                foreach ($options as $option) {
                    if ($option['label'] == $valueText) {
                        $valueText = $option['value'];
                    }
                }
            }

            $fieldName = Mage::getResourceSingleton('enterprise_search/engine')
                ->getSearchEngineFieldName($attribute, 'nav');
			
            $this->getLayer()->getProductCollection()->addFqFilter(array($fieldName => $value));

            return $this;
		}
		
        if (empty($value)) {
            $value = array();
        } else if (!is_array($value)) {
            $value = array($value);
        }

        $productCollection = $this->getLayer()->getProductCollection();
        $attribute  = $filter->getAttributeModel();

        $param = Mage::helper('enterprise_search')->getSearchParam($productCollection, $attribute, $value);
        $productCollection->addSearchQfFilter($param);
        return $this;
	
    }

    protected static function _addFilterValues($attributeId, array $productIds, array $attributeValues)
    {
        self::$_filterProducts[$attributeId]   = $productIds;
        self::$_filterAttributes[$attributeId] = $attributeValues;
    }

    public static function getFilterAttributes()
    {
        return self::$_filterAttributes;
    }

    public static function getFilterProducts()
    {
        return self::$_filterProducts;
    }

    public static function cleanFilterAttributes()
    {
        self::$_filterAttributes = array();
        self::$_filterProducts   = array();
    }
} } 