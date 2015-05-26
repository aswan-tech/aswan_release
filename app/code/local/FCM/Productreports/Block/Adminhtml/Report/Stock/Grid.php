<?php
set_time_limit(0);
class FCM_Productreports_Block_Adminhtml_Report_Stock_Grid extends FCM_Productreports_Block_Adminhtml_Report_Grid
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

		$collection = Mage::getModel('catalog/product')
						->getCollection()						
						->addAttributeToSelect(array('name','sku','ean','color','size','gender','price','special_price'));
		
		$filterStockFrom = $this->getFilter('product_stock_from');
		$filterStockTo = $this->getFilter('product_stock_to');
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
		
		$cdt = "";
		
		if ($filterStockFrom != '') {
			//$collection->addAttributeToFilter('qty', array('gteq' => $filterStockFrom));
			$cdt .= " and `qty` >= ". $filterStockFrom;
		}

		if ($filterStockTo != '') {
			//$collection->addAttributeToFilter('qty', array('lteq' => $filterStockTo));
			$cdt .= " and `qty` <= ". $filterStockTo;
		}				
		
		$collection->getSelect()->join(array('stock'=>'cataloginventory_stock_item'),'stock.product_id = e.entity_id' . $cdt,  array('stock.qty'));
		
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
		
		$this->addColumn('color', array(
            'header'    =>Mage::helper('productreports')->__('Color'),
            'width'     =>'250px',
            'index'     =>'color',
			'type'		=> 'options',
			'options'	=> Mage::helper('productreports')->getAttributeOptions('color'),
			'sortable'  => false
        ));
		
		$this->addColumn('size', array(
            'header'    =>Mage::helper('productreports')->__('Size'),
            'width'     =>'250px',
            'index'     =>'size',
			'sortable'  => false
        ));
	$this->addColumn('gender', array(
            'header'    =>Mage::helper('productreports')->__('Gender'),
            'width'     =>'250px',
            'index'     =>'gender',
            'type'     =>'options',
	    'options' 	=> array('319'=>'Women','320' => 'Men'),
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

        $this->addColumn('special_price', array(
            'header'    =>Mage::helper('productreports')->__('Special Price'),
            'width'     =>'150px',
            'align'     =>'right',
            'index'     =>'special_price',
			'currency_code' => $currencyCode,
            'type'      => 'currency',
            'total'     => 'sum',
            'sortable'  => false,
        ));
		
		$this->addColumn('qty', array(
            'header'    =>Mage::helper('productreports')->__('Avaiable Stock'),
            'index'     =>'qty',
			'width'     =>'150px',
			'type'		=> 'number',
			'sortable'  => false
        ));
	
        $this->addExportType('*/*/exportStockCsv', Mage::helper('productreports')->__('CSV'));
        $this->addExportType('*/*/exportStockXml', Mage::helper('productreports')->__('Excel XML'));
		
        return parent::_prepareColumns();
    }
}
