<?php

class FCM_Logger_Block_Adminhtml_Cron_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('cronGrid'); 
      $this->setDefaultSort('cron_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('logger/cron')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('cron_id', array(
          'header'    => Mage::helper('logger')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'cron_id',
      ));
	  
	  $this->addColumn('cron_name', array(
          'header'    => Mage::helper('logger')->__('Cron Name'),
          'align'     =>'left',
          'index'     => 'cron_name',
      ));
	  
	  $this->addColumn('start_time', array(
          'header'    => Mage::helper('logger')->__('Start Time'),
          'align'     =>'left',
          'index'     => 'start_time',
      ));

      $this->addColumn('finish_time', array(
			'header'    => Mage::helper('logger')->__('Finished Time'),
			'width'     => '150px',
			'index'     => 'finish_time',
      ));
	  
	  $this->addColumn('status', array(
          'header'    => Mage::helper('logger')->__('Status'),
          'align'     =>'left',
          'index'     => 'status',
		  'type'      => 'options',
          'options'   => array(
              'Finished' => 'Finished',
              'Failed' => 'Failed',
			  'Processing' => 'Processing',
          ),
      ));
	  $this->addColumn('message', array(
          'header'    => Mage::helper('logger')->__('Message'),
          'align'     =>'left',
          'index'     => 'message',
      ));
	  
		
		$this->addExportType('*/*/exportCsv', Mage::helper('logger')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('logger')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('cron_id');
        $this->getMassactionBlock()->setFormFieldName('cron');

       /* $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('logger')->__('Delete'),
             'url'      => $this->getUrl('*//*/massDelete'),
             'confirm'  => Mage::helper('logger')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('logger/status')->getOptionArray();

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