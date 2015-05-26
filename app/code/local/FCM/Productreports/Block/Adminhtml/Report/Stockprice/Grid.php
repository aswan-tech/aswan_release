<?php

class FCM_Productreports_Block_Adminhtml_Report_Stockprice_Grid extends FCM_Productreports_Block_Adminhtml_Report_Grid
{
     public function __construct()
    {
        parent::__construct();
    }
 
    protected function _prepareCollection()
    {
        $filter = $this->getParam($this->getVarNameFilter(), null);
		
		if (is_null($filter)) {
            $filter = $this->_defaultFilter;
        }

        if (is_string($filter)) {
            $data = array();
            $filter = base64_decode($filter);
            parse_str(urldecode($filter), $data);

            $this->_setFilterValues($data);
        } else if ($filter && is_array($filter)) {
            $this->_setFilterValues($filter);
        } else if(0 !== sizeof($this->_defaultFilter)) {
            $this->_setFilterValues($this->_defaultFilter);
        }		
		
		//Array ( [product_sku] => 2323223 [product_category] => 3 [product_sub_category] => 10 ) 

		$collection = Mage::getModel('catalog/product')
						->getCollection()						
						->addAttributeToSelect(array('name','sku','ean','price','discount'/*'special_price','special_from_date','special_to_date'*/));
		
		$filterStockFrom = $this->getFilter('product_stock_from');
		$filterStockTo = $this->getFilter('product_stock_to');
		//$filterSku = $this->getFilter('product_sku');
		$filterCategory = $this->getFilter('product_category');
		$filterSubCategory = $this->getFilter('product_sub_category');
		$filterProductType = $this->getFilter('product_type');
		
		if (!empty($filterCategory)) {
			$category = Mage::getModel('catalog/category')->load($filterCategory);
			$collection->addCategoryFilter($category);
		}
		
		if (!empty($filterSubCategory)) {
			$subcategory = Mage::getModel('catalog/category')->load($filterSubCategory);
			$collection->addCategoryFilter($subcategory);
		}
		/*
		if (!empty($filterSku)) {
			$collection->addAttributeToFilter('sku', $filterSku);
		}
		*/
		$cdt = "";
		
		if ($filterStockFrom != '') {
			//$collection->addAttributeToFilter('qty', array('gteq' => $filterStockFrom));
			$cdt .= " and `qty` >= ". $filterStockFrom;
		}

		if ($filterStockTo != '') {
			//$collection->addAttributeToFilter('qty', array('lteq' => $filterStockTo));
			$cdt .= " and `qty` <= ". $filterStockTo;
		}
		
		$collection->getSelect()->join(array('stock'=>'cataloginventory_stock_item'),'stock.product_id = e.entity_id'. $cdt,  array('stock.qty'));			
			
		//$collection->getSelect()->joinLeft(array('config'=>'catalog_product_super_link'),'e.entity_id = config.product_id',  array('ifnull(`config`.`parent_id`, `e`.`entity_id`) as super'));
		
		if (!empty($filterProductType)) {
			$collection->addAttributeToFilter('type_id', $filterProductType);
		}
		//$collection->addAttributeToFilter('type_id', 'simple');
						//->load();

		//$collection->getSelect()->order( array('super ASC', 'type_id ASC') );
		$collection->getSelect()->order(array(`e`.'entity_id'));
		
		$this->setCollection($collection);
		return parent::_prepareCollection();
    }
	
    protected function _prepareColumns()
    {
		$this->addColumn('name', array(
            'header'    =>Mage::helper('productreports')->__('Product Name'),
            'width'     =>'250px',
            'index'     =>'name',
			'sortable'  => false
        ));
		
		$this->addColumn('category', array(
            'header'    =>Mage::helper('productreports')->__('Category'),
            'width'     =>'250px',
            'index'     =>'sku',
			'renderer' => 'FCM_Productreports_Block_Adminhtml_Report_Renderer_Department',
			'sortable'  => false
        ));
		
		$this->addColumn('subcategory', array(
            'header'    =>Mage::helper('productreports')->__('Sub Category'),
            'width'     =>'250px',
            'index'     =>'sku',
			'renderer' => 'FCM_Productreports_Block_Adminhtml_Report_Renderer_Category',
			'sortable'  => false
        ));
		
		$this->addColumn('configsku', array(
            'header'    => Mage::helper('productreports')->__('Config SKU'),
            'width'     =>'250px',
            'index'     =>'sku',
			'renderer' => 'FCM_Productreports_Block_Adminhtml_Report_Renderer_Configurable',
			'sortable'  => false
        ));
		
		$this->addColumn('sku', array(
            'header'    => Mage::helper('productreports')->__('Simple SKU'),
            'width'     =>'250px',
            'index'     =>'sku',
			'renderer' => 'FCM_Productreports_Block_Adminhtml_Report_Renderer_Simple',
			'sortable'  => false
        ));

        $this->addColumn('ean', array(
            'header'    =>Mage::helper('productreports')->__('EAN'),
            'index'     =>'ean',
			'sortable'  => false
        ));
		
		$currencyCode = $this->getCurrentCurrencyCode();

        $this->addColumn('price', array(
            'header'    =>Mage::helper('productreports')->__('Price'),
            'width'     =>'150px',
            'align'     =>'right',
            'index'     =>'price',
			'currency_code' => $currencyCode,
			'type'      => 'currency',
            'total'     => 'sum',
            'sortable'  => false
        ));

/*        $this->addColumn('special_price', array(
            'header'    =>Mage::helper('productreports')->__('Special Price'),
            'width'     =>'150px',
            'align'     =>'right',
            'index'     =>'special_price',
			'currency_code' => $currencyCode,
            'type'      => 'currency',
            'total'     => 'sum',
            'sortable'  => false,
        ));
*/		
		$this->addColumn('qty', array(
            'header'    =>Mage::helper('productreports')->__('Qty'),
            'index'     =>'qty',
			'width'     =>'150px',
			'type'		=> 'number',
			'sortable'  => false
        ));

	$this->addColumn('discount', array(
            'header'    =>Mage::helper('productreports')->__('Discount'),
            'index'     =>'discount',
        	'width'     =>'150px',
		'sortable'  =>false,
		'type'  => 'options',
		'options' => $this->getDiscountValue()
        ));
		
/*		$this->addColumn('special_from_date', array(
            'header'        => Mage::helper('productreports')->__('Special Price From Date'),
            'index'         => 'special_from_date',
            'width'         => 150,
            'sortable'      => false,
			'type'      	=> 'date',
        ));
		
		$this->addColumn('special_to_date', array(
            'header'        => Mage::helper('productreports')->__('Special Price To Date'),
            'index'         => 'special_to_date',
            'width'         => 150,
            'sortable'      => false,
			'type'      	=> 'date',
        ));
*/	
        $this->addExportType('*/*/exportStockpriceCsv', Mage::helper('productreports')->__('CSV'));
        $this->addExportType('*/*/exportStockpriceXml', Mage::helper('productreports')->__('Excel XML'));
		
        return parent::_prepareColumns();
    }
	
protected function getDiscountValue()
{
	 return array(
            507   => Mage::helper('catalog')->__('5%'),
            508   => Mage::helper('catalog')->__('10%'),
            509   => Mage::helper('catalog')->__('15%'),
            510   => Mage::helper('catalog')->__('20%'),
            511   => Mage::helper('catalog')->__('25%'),
            512   => Mage::helper('catalog')->__('30%'),
            513   => Mage::helper('catalog')->__('35%'),
            514   => Mage::helper('catalog')->__('40%'),
            515   => Mage::helper('catalog')->__('45%'),
            652   => Mage::helper('catalog')->__('47%'),
            516   => Mage::helper('catalog')->__('50%'),
            517   => Mage::helper('catalog')->__('55%'),
            518   => Mage::helper('catalog')->__('60%'),
            519   => Mage::helper('catalog')->__('65%'),
            505   => Mage::helper('catalog')->__('70%'),
            506   => Mage::helper('catalog')->__('75%'),
            703   => Mage::helper('catalog')->__('0%'),
        );

	
}

}
