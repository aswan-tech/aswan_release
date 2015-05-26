<?php
class FCM_Productreports_Block_Adminhtml_Report_Workflow_Grid extends FCM_Productreports_Block_Adminhtml_Report_Grid
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
						->addAttributeToSelect('*');
		
		//Get Filters from url		
		
		$filterCategory = $this->getFilter('product_category');
		$filterSubCategory = $this->getFilter('product_sub_category');	
		$filterVisibility = $this->getFilter('product_visibility');
		$filterName = $this->getFilter('product_name');
		$filterStock = $this->getFilter('product_stock');
		$filterSku = $this->getFilter('product_sku');
		$filterProductType = $this->getFilter('product_type');
		
		//Filter Collection by Category/SubCategory
		if (!empty($filterCategory)) {
			$category = Mage::getModel('catalog/category')->load($filterCategory);
			$collection->addCategoryFilter($category);
		}
		
		if (!empty($filterSubCategory)) {
			$subcategory = Mage::getModel('catalog/category')->load($filterSubCategory);
			$collection->addCategoryFilter($subcategory);
		}
		
		//Filter Collection by ProductName
		if(!empty($filterName)) {
			$collection->addAttributeToFilter(array(array('attribute'=>'name', 'like'=>'%'. $filterName.'%')));
		}
		
		//Filter Collection by Sku
		if(!empty($filterSku)) {
			$collection->addAttributeToFilter(array(array('attribute'=>'sku', 'like'=>'%'.$filterSku.'%')));
		}
				
		$collection->addExpressionAttributeToSelect(
                     'frontendvisibitity',
                     'IF(`at_status`.`value` = 1, IF((type_id=\'configurable\' and (`at_visibility`.`value`=2 OR `at_visibility`.`value`=3 OR `at_visibility`.`value`=4) and is_in_stock = 1) OR (type_id=\'simple\' and (`at_visibility`.`value`=1) and `cataloginventory_stock_item`.`qty` > 0  and is_in_stock = 1), \'Yes\', \'No\'), \'No\')',
                     array('type_id', 'status', 'visibility'));
		
		$collection->addExpressionAttributeToSelect(
                     'stockstatus',
                     'IF((type_id=\'configurable\' and is_in_stock = 1) OR (type_id=\'simple\' and `cataloginventory_stock_item`.`qty` > 0  and is_in_stock = 1), \'Yes\', \'No\')',
                     array('type_id'));
		
		
		
		//Filter Collection by Visibility
		if(isset($filterVisibility)){
			if($filterVisibility == 1) { //Yes				
				$collection->addAttributeToFilter('frontendvisibitity', array('eq'=>'Yes'));
			} elseif($filterVisibility == 2) {//No
				$collection->addAttributeToFilter('frontendvisibitity', array('eq'=>'No'));
			}
		
		}
			
		$cond=null;
		
		$collection->joinTable('cataloginventory/stock_item', 'product_id = entity_id', array('qty','is_in_stock'), $cond);
		
		//Filter Collection by Stock
		if(isset($filterStock)){
			if($filterStock == 1) { //Yes				
				$collection->addAttributeToFilter('stockstatus', array('eq'=>'Yes'));
			} elseif($filterStock == 2) {//No
				$collection->addAttributeToFilter('stockstatus', array('eq'=>'No'));
			}
		
		}
		
		
		//$collection->getSelect()->joinLeft(array('config'=>'catalog_product_super_link'),'e.entity_id = config.product_id',  array('ifnull(`config`.`parent_id`, `e`.`entity_id`) as super'));
		
		
		if (!empty($filterProductType)) {
			$collection->addAttributeToFilter('type_id', $filterProductType);
		}
		
		//$collection->getSelect()->group(`e`.'sku');
		//$collection->getSelect()->order( array('super ASC', 'type_id ASC') );
		$collection->getSelect()->order(array(`e`.'entity_id'));
		
		//print $collection->getSelect();
		$this->setCollection($collection);
		return parent::_prepareCollection();
    }
	
    protected function _prepareColumns()
    {        
		$requestData = Mage::helper('adminhtml')->prepareFilterString($this->getRequest()->getParam('filter'));
		
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
		
		//if($requestData['product_type'] == 'configurable' || $requestData['product_type'] == ''){
			$this->addColumn('configsku', array(
				'header'    => Mage::helper('productreports')->__('Config SKU'),
				'width'     =>'250px',
				'index'     =>'sku',
				'renderer' => 'FCM_Productreports_Block_Adminhtml_Report_Renderer_Configurable',
				'sortable'  => false
			));
		//}
		
		//if($requestData['product_type'] == 'simple' || $requestData['product_type'] == ''){
			$this->addColumn('sku', array(
				'header'    => Mage::helper('productreports')->__('Simple SKU'),
				'width'     =>'250px',
				'index'     =>'sku',
				'renderer' => 'FCM_Productreports_Block_Adminhtml_Report_Renderer_Simple',
				'sortable'  => false
			));
		//}
		
		$this->addColumn('ean', array(
			'header' => Mage::helper('productreports')->__('EAN'),
			'index' => 'ean',
			'type' => 'text',
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
		
		$this->addColumn('name', array(
			'header' => Mage::helper('productreports')->__('Product Name'),
			'index' => 'name',
			'type' => 'text',
			'sortable'  => false
		));
		
		$this->addColumn('prod_content', array(
			'header' => Mage::helper('productreports')->__('Content'),
			'index' => 'entity_id',
			'type' => 'text',
			'width' => '70px',
			'renderer' => 'FCM_Productreports_Block_Adminhtml_Report_Workflow_Renderer_Content',
			'sortable'  => false
		));
		
		$this->addColumn('prod_images', array(
			'header' => Mage::helper('productreports')->__('Images'),
			'index' => 'entity_id',
			'type' => 'text',
			'renderer' => 'FCM_Productreports_Block_Adminhtml_Report_Workflow_Renderer_Images',
			'sortable'  => false
		));	
	
		$this->addColumn('stockstatus', array(
			'header' => Mage::helper('productreports')->__('Stock'),
			'index' => 'stockstatus',
			'sortable'  => false
		));
	
		$this->addColumn('frontendvisibitity', array(
			'header' => Mage::helper('productreports')->__('Visible'),
			'index' => 'frontendvisibitity',
			'sortable'  => false
		));
		
		$this->addExportType('*/*/exportWorkflowCsv', Mage::helper('productreports')->__('CSV'));
        $this->addExportType('*/*/exportWorkflowXml', Mage::helper('productreports')->__('Excel XML'));
		
        return parent::_prepareColumns();
    }
}