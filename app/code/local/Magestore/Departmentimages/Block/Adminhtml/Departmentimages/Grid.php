<?php

class Magestore_Departmentimages_Block_Adminhtml_Departmentimages_Grid extends Mage_Adminhtml_Block_Widget_Grid
{ 
  public function __construct()
  {
      parent::__construct();
      $this->setId('departmentimagesGrid');
      $this->setDefaultSort('departmentimages_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('departmentimages/departmentimages')->getCollection();
	  
	  /* $collection->getSelect()->join( array('cat'=>'catalog_category_entity_varchar'), 'main_table.category = cat.entity_id',
	  array('cat.value'));
		
	  $collection->getSelect()->group('main_table.category'); */
	  
	  // $collection->load(true);
	  
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('departmentimages_id', array(
          'header'    => Mage::helper('departmentimages')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'departmentimages_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('departmentimages')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));

	  /*
      $this->addColumn('content', array(
			'header'    => Mage::helper('departmentimages')->__('Item Content'),
			'width'     => '150px',
			'index'     => 'content',
      ));
	  */
	
		/* $this->addColumn('category', array(
          'header'    => Mage::helper('departmentimages')->__('Department'),
		  'width'     => '300px',
          'align'     => 'left',
          'index'     => 'value',
		)); */
	  
      $this->addColumn('status', array(
          'header'    => Mage::helper('departmentimages')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
	  
	  $this->addColumn('sort_id', array(
          'header'    => Mage::helper('departmentimages')->__('Sort Id'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'sort_id',          
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('departmentimages')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('departmentimages')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('departmentimages')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('departmentimages')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('departmentimages_id');
        $this->getMassactionBlock()->setFormFieldName('departmentimages');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('departmentimages')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('departmentimages')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('departmentimages/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('departmentimages')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('departmentimages')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}