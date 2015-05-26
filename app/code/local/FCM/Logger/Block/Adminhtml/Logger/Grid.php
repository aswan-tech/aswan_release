<?php

class FCM_Logger_Block_Adminhtml_Logger_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('loggerGrid');
      $this->setDefaultSort('logger_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('logger/logger')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('logger_id', array(
          'header'    => Mage::helper('logger')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'logger_id',
      ));
	  
	  $this->addColumn('log_time', array(
          'header'    => Mage::helper('logger')->__('Time'),
          'align'     =>'left',
          'index'     => 'log_time',
      ));
	  
	  $this->addColumn('module_name', array(
          'header'    => Mage::helper('logger')->__('Module Name'),
          'align'     =>'left',
          'index'     => 'module_name',
      ));

      $this->addColumn('description', array(
			'header'    => Mage::helper('logger')->__('Description'),
			'width'     => '300px',
			'index'     => 'description',
			'type'		=> 'content',
			'renderer'	=> 'FCM_Logger_Block_Adminhtml_Grid_Renderer_Content',
      ));
	  
	  $this->addColumn('filename', array(
          'header'    => Mage::helper('logger')->__('File Name'),
          'align'     =>'left',
          'index'     => 'filename',
      ));
	  
	
      $this->addColumn('status', array(
          'header'    => Mage::helper('logger')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              'Exception' => 'Exception',
              'Error' => 'Error',
			  'Warning' => 'Warning',
			  'Success' => 'Success',
			  'Failure' => 'Failure',
			  'Information' => 'Information',
          ),
      ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('logger')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('logger')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('logger_id');
        $this->getMassactionBlock()->setFormFieldName('logger');

        /*$this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('logger')->__('Delete'),
             'url'      => $this->getUrl('*//*/massDelete'),
             'confirm'  => Mage::helper('logger')->__('Are you sure?')
        ));*/

     /*   $statuses = Mage::getSingleton('logger/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('logger')->__('Change status'),
             'url'  => $this->getUrl('*//*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('logger')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));*/
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}