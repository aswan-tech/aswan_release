<?php
/**
 * Product:     Layered Navigation Pro for Enterprise Edition - 16/08/12
 * Package:     AdjustWare_Nav_10.4.9_10.0.0_557110
 * Purchase ID: hXgQU3oI8FOfJ8PDCk5s6h6XPM5oH2Kx0N6TCAPkqN
 * Generated:   2013-04-22 06:59:44
 * File path:   app/code/local/AdjustWare/Nav/Model/Catalog/Layer/Filter/Price.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'AdjustWare_Nav')){ kUrqBiwjDyIZBmrk('6b71f17e6d0b07a294dc12a1037c0cc2'); ?><?php
    class AdjustWare_Nav_Model_Catalog_Layer_Filter_Price extends Mage_Catalog_Model_Layer_Filter_Price
    {
        protected $baseSelect = null;
        protected $_isPricesFilterUpdated = false;
        protected $_priceDelimeter = ',';
		protected $_divisible = true;//for >= 1.12 with SOLR
		protected $_facets = array();//for >= 1.12 with SOLR
        
        public function __construct()
        {
            parent::__construct();
            if(method_exists($this, '_validateFilter')) {
                //magento 1.7+, 1.12+
                $this->_isPricesFilterUpdated = true;
                $this->_priceDelimeter = '-';
            }
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

        
        protected function _getItemsData()
        {
            $data = array();   
            $style = Mage::getStoreConfig('design/adjnav/price_style'); 
            
            if ('default' == $style)
            {
                if ($this->getMaxPriceInt() || version_compare( Mage::getVersion(),'1.12.0.0','>=' ))
                {
                    if ($this->getAttributeModel()->getAttributeCode() == 'price')
                    {
                        //SOLR COMPATIBILITY .
                        if(Mage::helper('adjnav')->isSolrEnabled())
                        {
						    if(version_compare( Mage::getVersion(),'1.12.0.0','<' ))
							{
                            $range  = $this->getPriceRange();
                            $facets = $this->getLayer()->getProductCollection()->getFacetedData($this->_getFilterField());

                            if (!empty($facets)) {
                                foreach ($facets as $key => $count) {
                                    preg_match('/TO ([\d\.]+)\]$/', $key, $rangeKey);
                                    $rangeKey = $rangeKey[1] / $range;
                                    if ($count > 0) {
                                        $rangeKey = round($rangeKey);
                                        $data[] = array(
                                            'label' => $this->_renderItemLabel($range, $rangeKey),
                                            'value' => $rangeKey . ',' . $range,
                                            'count' => $count
                                        );
                                    }
                                }
                            }
							}
							else
							{
							    $data = $this->_getItemsData12();
							}
                        } else {
                            $data = parent::_getItemsData();
                        }
                        //SOLR COMPATIBILITY .
                        if(sizeof($data)==0 && $this->getInterval()) {
                            //for magento 1.7+/1.12+ to show only one Price filter
                            list($fromPrice, $toPrice) = $this->getInterval();
                            $data[] = array(
                                'label' => $this->_renderRangeLabel($fromPrice, $toPrice),
                                'value' => $fromPrice . $this->_priceDelimeter . $toPrice,
                                'count' => -1, //will be updated by collectionSize value in AdjustWare_Nav_Block_Catalog_Layer_Filter_Price
                            );

                        }

                        //start fix for 28140 bug
                        if(sizeof($data) == 0)
                        {
                            list($from, $to) = $this->getFilterValueFromRequest();
                            $from = sprintf("%u", $from);
                            $to = sprintf("%u", $to);
                            $label = $this->_renderItemLabel($to, $from);
                            $data[] = array(
                                'label' => $label,
                                'value' => $from . $this->_priceDelimeter . $to,
                                'count' => 0,
                                 );
                        }
                        //end fix for 28140 bug

                        return $data;
                    }
                    elseif($this->getMaxPriceInt()) 
                    {
                        return $this->_getDecimalItemsData();
                    }
                }
                else 
                {
                    return array();
                }
            }
            elseif('input' == $style){
                list($from, $to) = $this->getFilterValueFromRequest();
                $data[] = array(
                    'label' => '',
                    'value' => $from . $this->_priceDelimeter . $to,
                    'count' => 1,
                );
            }    
            elseif('slider' == $style){
                $data[] = array(
                    'label' => '',
                    'value' => 0 . $this->_priceDelimeter . $this->getMaxPriceInt()+1,
                    'count' => 1,
                );
            }    
            
            return $data;
        }
        
		//for Magento EE >= 1.12
		protected function _getItemsData12()
    {
        if (!$this->_divisible) {
            return array();
        }

        $isAuto = (Mage::app()->getStore()
            ->getConfig(self::XML_PATH_RANGE_CALCULATION) == self::RANGE_CALCULATION_IMPROVED);
        if (!$isAuto && $this->getInterval()) {
            return array();
        }

        $facets = $this->getLayer()->getProductCollection()->getFacetedData($this->_getFilterField());
        $data = array();
        if (!empty($facets)) {
            foreach ($facets as $k => $count) {
                if ($count <= 0) {
                    unset($facets[$k]);
                }
            }

            if (!$isAuto && !empty($facets)) {
                $range  = $this->getPriceRange();
            }

            $i = 0;
            $maxIntervalsNumber = $this->getMaxIntervalsNumber();
            $lastSeparator = null;
            foreach ($facets as $key => $count) {
                ++$i;
                preg_match('/\[([\d\.\\\*]+) TO ([\d\.\\\*]+)\]$/', $key, $separator);
                $separator[1] = str_replace('\\*', '*', $separator[1]);
                $separator[2] = str_replace('\\*', '*', $separator[2]);

                $label = null;
                $value = null;
                if (isset($this->_facets[$separator[1] . '_' . $separator[2]])) {
                    $separatorLabelValues = $this->_facets[$separator[1] . '_' . $separator[2]];
                    if ($i <= max(1, $maxIntervalsNumber)) {
                        $lastSeparator = $separatorLabelValues[0];
                    }
                    $label = $this->_renderRangeLabel($separatorLabelValues[0], $separatorLabelValues[1]);
                    $value = (empty($separatorLabelValues[0]) ? '' : $separatorLabelValues[0])
                        . '-' . $separatorLabelValues[1];
                }

                if ($isAuto) {
                    if ($separator[1] == '*') {
                        $separator[1] = '';
                    }
                    if ($separator[2] == '*') {
                        $separator[2] = '';
                    }
                } else {
                    $rangeKey = $separator[2] / $range;

                    $rangeKey = round($rangeKey, 2);
                    $separator[1] = ($rangeKey == 1) ? '' : (($rangeKey - 1) * $range);
                    $separator[2] = ($key == null) ? '' : ($rangeKey * $range);
                    // checking max number of intervals
                    if ($i > 1 && $i > $maxIntervalsNumber) {
                        --$i;
                        $count += $data[$i - 1]['count'];
                        $separator[1] = $data[$i - 1]['from'];
                        $label = $value = null;
                    } elseif (!empty($separator[2]) && $separator[2] > $this->getMaxPriceInt()) {
                        $label = $value = null;
                        $separator[2] = '';
                    }
                }

                $data[$i - 1] = array(
                    'label' => is_null($label) ? $this->_renderRangeLabel(
                            empty($separator[1]) ? 0 : ($separator[1] * $this->getCurrencyRate()),
                            empty($separator[2]) ? $separator[2] : $separator[2]  * $this->getCurrencyRate()
                        ) : $label,
                    'value' => (is_null($value) ? ($separator[1] . '-' . $separator[2]) : $value)
                        . $this->_getAdditionalRequestData(),
                    'count' => $count,
                    'from'  => $separator[1],
                    'to'    => $separator[2],
                );
            }

            if (isset($data[$i - 1]) && $data[$i - 1]['from'] != $data[$i - 1]['to']) {
                $upperIntervalLimit = '';
                $appliedInterval = $this->getInterval();
                if ($appliedInterval) {
                    $upperIntervalLimit = $appliedInterval[1];
                }
                if (is_null($value)) {
                    $data[$i - 1]['value'] = $lastSeparator . '-' . $upperIntervalLimit
                        . $this->_getAdditionalRequestData();
                }
                if (is_null($label)) {
                    $data[$i - 1]['label'] = $this->_renderRangeLabel(
                        empty($lastSeparator) ? 0 : $lastSeparator,
                        $upperIntervalLimit
                    );
                }
            }
        }

        return $data;
    }
        
        protected function _getCacheKey()
        {
            $key = parent::_getCacheKey();
            $key .= Mage::getStoreConfig('design/adjnav/price_style');
            $key .= Mage::helper('adjnav')->getCacheKey('price');
            return $key;
        }   
        
        private function getFilterValueFromRequest()
        {
            $filter = Mage::helper('adjnav')->getParam($this->_requestVar);
            $default = array(0, 0);

            if (!$filter) {
                return $default;
            }

            if($this->_isPricesFilterUpdated && 'default' == Mage::getStoreConfig('design/adjnav/price_style')) {
                //magento 1.7+ style
                $filterParams = explode(',', $filter);
                $filter = $this->_validateFilter($filterParams[0]);
                if (!$filter) {
                    return $default;
                }
            } else {
                $filter = explode(',', $filter);//magento 1.6-, and price slider/input for all magentos
                if (count($filter) != 2) {
                    return $default;
                }
            }

            list($from, $to) = $filter;
            $from = sprintf("%.02f", $from);
            $to   = sprintf("%.02f", $to);

            return array($from, $to);
        }

        /**
         * Apply price range filter to collection
         *
         * @return Mage_Catalog_Model_Layer_Filter_Price
         */
        public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
        {
            list($from, $to) = $this->getFilterValueFromRequest(); 
            if ('default' == Mage::getStoreConfig('design/adjnav/price_style'))
            {
                if (($this->getAttributeModel()->getAttributeCode() != 'price') && ($this->getAttributeModel()->getFrontendInput() == 'price') && version_compare(Mage::getVersion(),'1.12.0.0','>='))
                {}
                else
                {
                    if($this->_isPricesFilterUpdated) {
                    //magento 1.12+ style
                    $from = round($from);
                    $from = ($from != 0) ? $from : '';//like -600
                    $to = round($to);
                    $to = ($to != 0) ? $to : '';//2000 and above = '2000-'
                    $this->setActiveState($from.$this->_priceDelimeter.$to);
                    return parent::apply($request, $filterBlock);
                }
				}

                $index = $from;
                $rate  = $to;

                $from = ($index-1)*$rate;
                $to   = $index*$rate;

                $this->setActiveState(sprintf('%d'.$this->_priceDelimeter.'%d', $index, $rate));

            }
            else{
                $this->setActiveState($from . $this->_priceDelimeter . $to);
            }
            
            $this->baseSelect = clone $this->getLayer()->getProductCollection()->getSelect();
            if ($from >= 0.01 || $to >= 0.01) {
                $this->applyFromToFilter($from, $to);
                if(Mage::helper('adjnav')->isSolrEnabled())
                {
                    //added - 0.001 because off facet conditions and exclude ranges of SQL filtering in collection=)
                	$this->_applyToCollection($from, $to - 0.001);	
                } 
            }

            return $this;
        }
        
        // copied from Mage_CatalogIndex_Model_Mysql4_Price,
        // bacause of AWFULL!!! design: the method accept $range, $index INSTEAD OF $from, $to args
        // hope you, my reader, understand that is it incorrect in terms of tiers (library
        // function shouldn't know about logic of price ranges ...
        protected function applyFromToFilter($from, $to)
        {
            $attribute    = $this->getAttributeModel();
            
            $tableAlias = $attribute->getAttributeCode() . '_idx';
           
            if ($attribute->getAttributeCode() == 'price')
            {
                $bIsBasePrice = true;
    #            $tableAlias    = 'price_table_'.$attribute->getAttributeCode();
                $tableName = Mage::getSingleton('core/resource')->getTableName('catalogindex/price');
                $valueAlias = 'min_price';
            }
            else 
            {
                $bIsBasePrice = false;
                $tableName = Mage::getSingleton('core/resource')->getTableName('catalog/product_index_eav_decimal');
                $valueAlias = 'value';
            }
            
            $collection   = $this->getLayer()->getProductCollection();
            $websiteId    = Mage::app()->getStore()->getWebsiteId();
            $custGroupId  = Mage::getSingleton('customer/session')->getCustomerGroupId();
            
            /**
             * Distinct required for removing duplicates in case when we have grouped products
             * which contain multiple rows for one product id
             */
            
            $collection->getSelect()->distinct(true);
            
            $sOnStatement = $tableAlias . '.entity_id=e.entity_id';

            if (!$bIsBasePrice)
            {
                $sOnStatement .= ' AND ' . $tableAlias . '.attribute_id = ' .$attribute->getId() . ' AND ' . $tableAlias . '.store_id = ' . Mage::app()->getStore()->getId();                
            }
            
            $collection->getSelect()->joinLeft(
                array($tableAlias => $tableName), // modified line
                $sOnStatement,
                array()
            );

            $response = new Varien_Object();
            $response->setAdditionalCalculations(array());
            
            if ($bIsBasePrice)
            {
                $collection->getSelect()
                    ->where($tableAlias . '.website_id = ?', $websiteId)   // modified line
                    ;
            }   

            if ($attribute->getAttributeCode() == 'price') {
                $collection->getSelect()->where($tableAlias . '.customer_group_id = ?', $custGroupId); // modified line
                $args = array(
                    'select'         => $collection->getSelect(),
                    'table'          => $tableAlias,
                    'store_id'       => Mage::app()->getStore()->getId(), // modified line
                    'response_object'=> $response,
                );
                Mage::dispatchEvent('catalogindex_prepare_price_select', $args);
            }
            
            $rate =  $this->_getCurrencyRate();
            
            // make query a little bit faster
            if ($from > 0.01)
                $collection->getSelect()->where("(({$tableAlias}.{$valueAlias}".implode('', $response->getAdditionalCalculations()).")*$rate) >= ?", $from);
            if ($to > 0.01)    
                $collection->getSelect()->where("(({$tableAlias}.{$valueAlias}".implode('', $response->getAdditionalCalculations()).")*$rate) < ?", $to);

            $this->_isTableJoined = true;
    #d($collection->getSelect()->__toString()); 
            return $this;
        }  
        
        protected $_isTableJoined = null;
        
        protected function _getBaseCollectionSql()
        {
            return $this->baseSelect;
        }

        protected function _getCurrencyRate()
        {
            $rate =  Mage::app()->getStore()->convertPrice(1000000, false);
            
            $rate = $rate / 1000000;
            
            return $rate;
        }
        
        public function getMaxPriceInt() /// to do - make like minmax!!
        {
            
            list($min,$max) = $this->getMinMaxPriceInt();        
            
            return $max;
            /*
            $rate =  $this->_getCurrencyRate();
            
            $nMaxPrice = parent::getMaxPriceInt();
            
            // for some really UNKNOWN reason magento team 
            // rounds max price to lower value :)
            return 1 + $nMaxPrice * $rate;
            */
        }
        
        public function addFacetCondition()
        {
		    if(version_compare(Mage::getVersion(),'1.12.0.0','>='))
			{
			    if (Mage::app()->getStore()->getConfig(self::XML_PATH_RANGE_CALCULATION) == self::RANGE_CALCULATION_IMPROVED) {
            return $this->_addCalculatedFacetCondition();
        }

        $this->_facets = array();
        $range    = $this->getPriceRange();
        $maxPrice = $this->getMaxPriceInt();
		//this is the simple way to make it work
            if(Mage::getStoreConfig('design/adjnav/price_style') == 'slider')
            {
                Mage::register('price_range', $this->getMinMaxPriceInt());    
            }
        if ($maxPrice >= 0) {
            $priceFacets = array();
            $facetCount  = ceil($maxPrice / $range);

            for ($i = 0; $i < $facetCount + 1; $i++) {
                $separator = array($i * $range, ($i + 1) * $range);
                $facetedRange = $this->_prepareFacetRange($separator[0], $separator[1]);
                $this->_facets[$facetedRange['from'] . '_' . $facetedRange['to']] = $separator;
                $priceFacets[] = $facetedRange;
            }

            $this->getLayer()->getProductCollection()->setFacetCondition($this->_getFilterField(), $priceFacets);
        }

        
			}
			else
			{
            $range    = $this->getPriceRange();
            $maxPrice = $this->getMaxPriceInt();
            //this is the simple way to make it work
            if(Mage::getStoreConfig('design/adjnav/price_style') == 'slider')
            {
                Mage::register('price_range', $this->getMinMaxPriceInt());    
            }
            
            if ($maxPrice > 0) {
                $priceFacets = array();
                $facetCount  = ceil($maxPrice / $range);

                for ($i = 0; $i < $facetCount + 1; $i++) {
                    $to = ($i + 1) * $range;
                    if ($i < $facetCount) {
                        $to -= 0.001;
                    }
                    $priceFacets[] = array(
                        'from' => $i * $range,
                        'to'   => $to
                    );
                }

                $this->getLayer()->getProductCollection()->setFacetCondition($this->_getFilterField(), $priceFacets);
            }
			}

            return $this;
        }
        
        protected function getMinOrMax($filter, $bIsBasePrice, $minOfMaxFlag = 'min')
        {
            $attribute    = $this->getAttributeModel();
            $mainCollection   = $this->getLayer()->getProductCollection();
            $websiteId    = Mage::app()->getStore()->getWebsiteId();
            $custGroupId  = Mage::getSingleton('customer/session')->getCustomerGroupId();

    #        $tableAlias    = 'price_table';

            $select = clone $mainCollection->getSelect();

            $select->setPart(Varien_Db_Select::COLUMNS, array());
            $select->setPart(Varien_Db_Select::ORDER , array());

            $select->distinct(false);

            $tableAlias = $attribute->getAttributeCode() . '_idx';

            if ($bIsBasePrice)
            {
                $tableName = Mage::getSingleton('core/resource')->getTableName('catalogindex/price');
                $valueAlias = 'min_price';
            }
            else
            {
                $tableName = Mage::getSingleton('core/resource')->getTableName('catalog/product_index_eav_decimal');
                $valueAlias = 'value';
            }

            if($minOfMaxFlag === 'min')
            {
                $select->columns(array(
                    'value' => new Zend_Db_Expr('MIN(' . $tableAlias . '.' . $valueAlias . ')'),
                    'id' => new Zend_Db_Expr($tableAlias . '.entity_id'),
                ));
            }
            else
            {
                $select->columns(array(
                    'value' => new Zend_Db_Expr('MAX(' . $tableAlias . '.' . $valueAlias . ')'),
                    'id' => new Zend_Db_Expr($tableAlias . '.entity_id'),
                ));
            }

    #d($select->__toString(), 0);

            if ($this->_isTableJoined)
            {
                // 1) remove from conditions
                $oldWhere = $select->getPart(Varien_Db_Select::WHERE);

                $newWhere = array();

                $alias = $tableAlias . '.' . $valueAlias;

                foreach ($oldWhere as $cond){
                   if (!strpos($cond, $alias))
                   {
                       $newWhere[] = $cond;
                    }
                }

                if ($newWhere && substr($newWhere[0], 0, 3) == 'AND')
                   $newWhere[0] = substr($newWhere[0],3);

                $select->setPart(Varien_Db_Select::WHERE, $newWhere);
            }
            else
            {
                $sOnStatement = $tableAlias . '.entity_id=e.entity_id';

                if (!$bIsBasePrice)
                {
                    $sOnStatement .= ' AND ' . $tableAlias . '.attribute_id = ' .$attribute->getId() . ' AND ' . $tableAlias . '.store_id = ' . Mage::app()->getStore()->getId();
                }
                else
                {
    #                $sOnStatement .= ' AND ' . $tableAlias . '.website_id = ' . $websiteId . ' AND ' . $tableAlias . '.customer_group_id = ' . Mage::app()->getStore()->getId();
                }

                $select->joinLeft(
                    array($tableAlias => $tableName), // modified line
                    $sOnStatement,
                    array()
                );

                $response = new Varien_Object();
                $response->setAdditionalCalculations(array());

                if ($bIsBasePrice)
                {
                    $select
                        ->where($tableAlias . '.website_id = ?', $websiteId)   // modified line
                    ;
                }

                if ($bIsBasePrice)
                {
                    $select->where($tableAlias . '.customer_group_id = ?', $custGroupId); // modified line
                    $args = array(
                        'select'         => $select,
                        'table'          => $tableAlias,
                        'store_id'       => Mage::app()->getStore()->getId(), // modified line
                        'response_object'=> $response,
                    );
                    Mage::dispatchEvent('catalogindex_prepare_price_select', $args);
                }
            }
            if ($minOfMaxFlag === 'min')
            {
                $select->order('value ASC');
            }
            else
            {
                $select->order('value DESC');
            }
            $select->group('id');
            $select->limit(1);
//die($select->__toString());
            $adapter = Mage::getSingleton('core/resource')->getConnection('core_read');
            $result = $adapter->fetchRow($select);
            return $result;
        }
            

        protected function getMinMax($filter, $bIsBasePrice)
        {
            $taxHelper  = Mage::helper('tax');
            $rate =  $this->_getCurrencyRate();
            if (((integer) $taxHelper->getPriceDisplayType()) == 1)
            {
                $min = $this->getMinOrMax($filter, $bIsBasePrice, 'min');
                $minPrice = $min['value'] * $rate;
                $max = $this->getMinOrMax($filter, $bIsBasePrice, 'max');
                $maxPrice = ceil($max['value'] * $rate);
                return array($minPrice, $maxPrice);
            }
            
            
            $min = $this->getMinOrMax($filter, $bIsBasePrice, 'min');
            if($bIsBasePrice)
			{
            $product = Mage::getModel('catalog/product')->load($min['id']);
		    $minPrice = $taxHelper->getPrice($product, $product->getFinalPrice(), true) * $rate;
		    }
			else
			{
			    $minPrice = ceil($min['value'] * $rate);
            }
			
            $max = $this->getMinOrMax($filter, $bIsBasePrice, 'max');
	        if($bIsBasePrice)
			{
                $product = Mage::getModel('catalog/product')->load($max['id']);
                $maxPrice = ceil($taxHelper->getPrice($product, $product->getFinalPrice(), true) * $rate);
			}
			else
			{
			    $maxPrice = ceil($max['value'] * $rate);
			}
            return array($minPrice, $maxPrice);
        }   
        

        
        public function getMinMaxPriceInt()
        {
            $attribute    = $this->getAttributeModel();
            
            if ($attribute->getAttributeCode() == 'price')
            {
                list($min, $max) = $this->getMinMax($this, true);
            }
            else 
            {
                list($min, $max) = $this->getMinMax($this, false);
            }
            
            $max = floor($max);
            $min = floor($min);
            if(Mage::registry('price_range'))
            {
                return Mage::registry('price_range');
            }
            return array($min, $max);
        }
        
        // functions for additional prices (decimal in magento 1.4)
        
        /**
         * Retrieve data for build decimal filter items
         *
         * @return array
         */
        protected function _getDecimalItemsData()
        {
            $key = $this->_getDecimalCacheKey();

            $data = $this->getLayer()->getAggregator()->getCacheData($key);
            
            if ($data === null) {
                $data       = array();
                $range      = $this->getRange();
                $dbRanges   = $this->getDecimalRangeItemCounts($range);

                $rangesSize = count($dbRanges);
                $rangeIndex = 0;
                foreach ($dbRanges as $index => $count) {
                $rangeIndex ++;
                if (($this->getAttributeModel()->getAttributeCode() != 'price') && ($this->getAttributeModel()->getFrontendInput() == 'price') && version_compare(Mage::getVersion(),'1.12.0.0','>='))
                {
                    $from   = ($index - 1) * $range;
                    if($rangeIndex == $rangesSize)
                        $to = '';
                    else
                        $to = $index * $range;
                    $label = $this->_renderRangeLabel($from, $to);
                }
                else
                    $label = $this->_renderItemLabel($range, $index);
					
                    $data[] = array(
                        'label' => $label,
                        'value' => $index . $this->_priceDelimeter . $range,
                        'count' => $count,
                    );
                }
            }
            return $data;
        }
        
        const MIN_RANGE_POWER = 10;

        /**
         * Resource instance
         *
         * @var Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Decimal
         */
        protected $_resource;

        /**
         * Initialize filter and define request variable
         *
         */
        /*
        public function __construct()
        {
            parent::__construct();
            $this->_requestVar = 'decimal';
        }
    */
        /**
         * Retrieve resource instance
         *
         * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Decimal
         */
        protected function _getResource()
        {
            if (is_null($this->_resource)) {
                if ($this->getAttributeModel()->getAttributeCode() == 'price')
                {
                    if($this->_isPricesFilterUpdated) {
                        //magento 1.7/1.12
                        $this->_resource = Mage::getResourceModel('catalog/layer_filter_price');
                    } else {
                        $this->_resource = Mage::getResourceModel('adjnav/catalog_layer_filter_price');
                    }
                }
                else 
                {
                    $this->_resource = Mage::getResourceModel('adjnav/catalog_layer_filter_decimal');
                }
            }
            return $this->_resource;
        }

        /**
         * Apply decimal range filter to product collection
         *
         * @param Zend_Controller_Request_Abstract $request
         * @param Mage_Catalog_Block_Layer_Filter_Decimal $filterBlock
         * @return Mage_Catalog_Model_Layer_Filter_Decimal
         */
        
        public function _______apply(Zend_Controller_Request_Abstract $request, $filterBlock)
        {
            parent::apply($request, $filterBlock);

            /**
             * Filter must be string: $index, $range
             */
            $filter = $request->getParam($this->getRequestVar());
            if (!$filter) {
                return $this;
            }

            $filter = explode(',', $filter);
            if (count($filter) != 2) {
                return $this;
            }

            list($index, $range) = $filter;
            if ((int)$index && (int)$range) {
                $this->setRange((int)$range);

                $this->_getResource()->applyFilterToCollection($this, $range, $index);  
                
                $this->getLayer()->getState()->addFilter(
                    $this->_createItem($this->_renderItemLabel($range, $index), $filter)
                );

                $this->_items = array();
            }

            return $this;
        }

        /**
         * Retrieve price aggreagation data cache key
         *
         * @return string
         */
        protected function _getDecimalCacheKey()
        {
            $key = $this->getLayer()->getStateKey()
                . '_ATTR_' . $this->getAttributeModel()->getAttributeCode();
            return $key;
        }
        
        protected function _applyToCollection($from, $to)
        {
            $value = array(
                $this->_getFilterField() => array(
                    'from' => $from,
                    'to'   => $to
                )
            );
    
            $this->getLayer()->getProductCollection()->addFqFilter($value);
    
            return $this;
        }

        /**
         * Prepare text of item label
         *
         * @param   int $range
         * @param   float $value
         * @return  string
         */
        protected function _renderItemLabel($range, $value)
        {
            $from   = Mage::app()->getStore()->formatPrice(($value - 1) * $range, false);
            $to     = Mage::app()->getStore()->formatPrice($value * $range, false);
            return Mage::helper('catalog')->__('%s - %s', $from, $to);
        }

        /**
         * Retrieve maximum value from layer products set
         *
         * @return float
         */
        public function getMaxValue()
        {
            $max = $this->getData('max_value');
            if (is_null($max)) {
    //            list($min, $max) = $this->_getResource()->getMinMax($this);
                list($min, $max) = $this->getMinMax($this, false);
                
                $this->setData('max_value', $max);
                $this->setData('min_value', $min);
            }
            return $max;
        }

        /**
         * Retrieve minimal value from layer products set
         *
         * @return float
         */
        public function getMinValue()
        {
            $min = $this->getData('min_value');
            if (is_null($min)) {
                list($min, $max) = $this->_getResource()->getMinMax($this);
                $this->setData('max_value', $max);
                $this->setData('min_value', $min);
            }
            return $min;
        }

        /**
         * Retrieve range for building filter steps
         *
         * @return int
         */
        public function getRange()
        {
            $range = $this->getData('range');
            if (is_null($range)) {
                $max = $this->getMaxValue();
                $index = 1;
                do {
                    $range = pow(10, (strlen(floor($max)) - $index));
                    $items = $this->getDecimalRangeItemCounts($range);
                    $index ++;
                }
                while($range > self::MIN_RANGE_POWER && count($items) < 2);

                $this->setData('range', $range);
            }
            return $range;
        }

        /**
         * Retrieve information about products count in range
         *
         * @param int $range
         * @return int
         */
        public function getDecimalRangeItemCounts($range)
        {
            $rangeKey = 'range_item_counts_' . $range;
            $items = $this->getData($rangeKey);
            if (is_null($items)) {
    #            $items = $this->_getResource()->getCount($this, $range);
                $items = $this->_getResourceCount($this, $range);
                $this->setData($rangeKey, $items);
            }
            return $items;
        }
        
        protected function _getResourceCount($filter, $range)
        {
            $select     = $this->_getDecimalSelect($filter);
            $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
    #        $connection = $this->_getResource()->_getReadAdapter();
    #        $response   = $this->_getResource()->_dispatchPreparePriceEvent($filter, $select);

    #        $table = $this->_getResource()->_getIndexTableAlias();

            $table = 'decimal_index';
            
    #        $additional = join('', $response->getAdditionalCalculations());

            $additional = '';

            $rate       = $filter->getCurrencyRate();
            $countExpr  = new Zend_Db_Expr("COUNT(*)");
            $rangeExpr  = new Zend_Db_Expr("FLOOR((({$table}.value {$additional}) * {$rate}) / {$range}) + 1");

            $select->columns(array(
                'range' => $rangeExpr,
                'count' => $countExpr
            ));
            $select->where("{$table}.value > 0");
            $select->group('range');
    #d($select->__toString(), 0); 
            return $connection->fetchPairs($select);
        }
        
        protected function _getDecimalSelect($filter)
        {
            $collection = $filter->getLayer()->getProductCollection();

            // clone select from collection with filters
            $select = clone $collection->getSelect();
            // reset columns, order and limitation conditions
            $select->reset(Zend_Db_Select::COLUMNS);
            $select->reset(Zend_Db_Select::ORDER);
            $select->reset(Zend_Db_Select::LIMIT_COUNT);
            $select->reset(Zend_Db_Select::LIMIT_OFFSET);

            $attributeId = $filter->getAttributeModel()->getId();
            $storeId     = $collection->getStoreId();

            $select->join(
                array('decimal_index' => $this->_getResource()->getMainTable()),
                "e.entity_id=decimal_index.entity_id AND decimal_index.attribute_id={$attributeId}"
                    . " AND decimal_index.store_id={$storeId}",
                array()
            );

            return $select;
        }

            /**
         * Get facet field name based on current website and customer group
         *
         * @return string
         */
        protected function _getFilterField()
        {
		    if(version_compare(Mage::getVersion(),'1.12.0.0','>=') && Mage::getConfig()->getModuleConfig('Enterprise_Search')->is('active', 'true')
            )
			{
			    $engine = Mage::getResourceSingleton('enterprise_search/engine');
                $priceField = $engine->getSearchEngineFieldName('price');
			}
			else
			{
            $websiteId       = Mage::app()->getStore()->getWebsiteId();
            $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
            $priceField      = 'price_'. $customerGroupId .'_'. $websiteId;
			}
    
            return $priceField;
        }
        
        protected function _addCalculatedFacetCondition()
    {
        $priceFacets = array();
        $this->_facets = array();
        foreach ($this->_getSeparators() as $separator) {
            $facetedRange = $this->_prepareFacetRange($separator[0], $separator[1]);
            $this->_facets[$facetedRange['from'] . '_' . $facetedRange['to']] = $separator;
            $priceFacets[] = $facetedRange;
        }
        $this->getLayer()->getProductCollection()->setFacetCondition($this->_getFilterField(), $priceFacets);
    }

        /**
     * Get data with price separators
     *
     * @return array
     */
    protected function _getSeparators()
    {
        $searchParams = $this->getLayer()->getProductCollection()->getExtendedSearchParams();
        $intervalParams = $this->getInterval();
        $intervalParams = $intervalParams ? ($intervalParams[0] . '-' . $intervalParams[1]) : '';
        $uniquePart = strtoupper(md5(serialize($searchParams . '_'
            . $this->getCurrencyRate() . '_' . $intervalParams)));
        $cacheKey = 'PRICE_SEPARATORS_' . $this->getLayer()->getStateKey() . '_' . $uniquePart;

        $cachedData = Mage::app()->loadCache($cacheKey);
        if (!$cachedData) {
            /** @var $algorithmModel Mage_Catalog_Model_Layer_Filter_Price_Algorithm */
            $algorithmModel = Mage::getSingleton('catalog/layer_filter_price_algorithm');
            $statistics = $this->getLayer()->getProductCollection()->getStats($this->_getFilterField());
            $statistics = $statistics[$this->_getFilterField()];

            $appliedInterval = $this->getInterval();
            if (
                $appliedInterval
                && ($statistics['count'] <= $this->getIntervalDivisionLimit()
                || $appliedInterval[0] == $appliedInterval[1]
                || $appliedInterval[1] === '0')
            ) {
                $algorithmModel->setPricesModel($this)->setStatistics(0, 0, 0, 0);
                $this->_divisible = false;
            } else {
                if ($appliedInterval) {
                    $algorithmModel->setLimits($appliedInterval[0], $appliedInterval[1]);
                }
                $algorithmModel->setPricesModel($this)->setStatistics(
                    round($statistics['min'] * $this->getCurrencyRate(), 2),
                    round($statistics['max'] * $this->getCurrencyRate(), 2),
                    $statistics['stddev'] * $this->getCurrencyRate(),
                    $statistics['count']
                );
            }

            $cachedData = array();
            foreach ($algorithmModel->calculateSeparators() as $separator) {
                $cachedData[] = $separator['from'] . '-' . $separator['to'];
            }
            $cachedData = implode(',', $cachedData);

            $tags = $this->getLayer()->getStateTags();
            $tags[] = self::CACHE_TAG;
            Mage::app()->saveCache($cachedData, $cacheKey, $tags);
        }

        if (!$cachedData) {
            return array();
        }

        $cachedData = explode(',', $cachedData);
        foreach ($cachedData as $k => $v) {
            $cachedData[$k] = explode('-', $v);
        }

        return $cachedData;
    }

    /**
     * Prepare price range to be added to facet conditions
     *
     * @param string|float $from
     * @param string|float $to
     * @return array
     */
    protected function _prepareFacetRange($from, $to)
    {
        if (empty($from)) {
            $from = '*';
        }

        if ($to === '') {
            $to = '*';
        } else {
            if ($to == $from || ($to == 0 && $from == '*')) {
                $to = $this->_prepareFacetedValue($to, false);
            } else {
                $to = $this->_prepareFacetedValue($to);
            }
        }

        if ($from != '*') {
            $from = $this->_prepareFacetedValue($from);
        }
        return array('from' => $from, 'to' => $to);
    }

    /**
     * Prepare faceted value
     *
     * @param float $value
     * @param bool $decrease
     * @return float
     */
    protected function _prepareFacetedValue($value, $decrease = true) {
        // rounding issue
        if ($this->getCurrencyRate() > 1) {
            if ($decrease) {
                $value -= Mage_Catalog_Model_Resource_Layer_Filter_Price::MIN_POSSIBLE_PRICE / 10;
            } else {
                $value += Mage_Catalog_Model_Resource_Layer_Filter_Price::MIN_POSSIBLE_PRICE / 10;
            }
            $value /= $this->getCurrencyRate();
        } else {
            $value /= $this->getCurrencyRate();
            if ($decrease) {
                $value -= Mage_Catalog_Model_Resource_Layer_Filter_Price::MIN_POSSIBLE_PRICE / 10;
            } else {
                $value += Mage_Catalog_Model_Resource_Layer_Filter_Price::MIN_POSSIBLE_PRICE / 10;
            }
        }
        return round($value, 3);
    }

protected function _applyPriceRange()
    {
	    if(version_compare(Mage::getVersion(),'1.12.0.0','>='))
		{
	        if(Mage::helper('adjnav')->isSolrEnabled())
		    {
                list($from, $to) = $this->getInterval();
                $this->getLayer()->getProductCollection()->addFqFilter(array(
                    $this->_getFilterField() => $this->_prepareFacetRange($from, $to)
                ));
        	
                return $this;
		    }
		    else
		        return parent::_applyPriceRange();
		}
    }	
    } } 