<?php

class Jextn_Testimonials_Block_Adminhtml_Testimonials_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('testimonialsGrid');
      $this->setDefaultSort('testimonials_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('testimonials/testimonials')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('testimonials_id', array(
          'header'    => Mage::helper('testimonials')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'testimonials_id',
      ));

      $this->addColumn('name', array(
          'header'    => Mage::helper('testimonials')->__('Name'),
          'align'     =>'left',
          'index'     => 'name',
      ));
	  
	  $this->addColumn('footer', array(
          'header'    => Mage::helper('testimonials')->__('Footer'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'footer',
          'type'      => 'options',
          'options'   => array(
              1 => 'Yes',
              0 => 'No',
          ),
      ));
	  
	  $this->addColumn('sidebar', array(
          'header'    => Mage::helper('testimonials')->__('Sidebar'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'sidebar',
          'type'      => 'options',
          'options'   => array(
              1 => 'Yes',
              0 => 'No',
          ),
      ));

	      $this->addColumn('status', array(
          'header'    => Mage::helper('testimonials')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Approved',
              2 => 'Pending',
          ),
      ));
	  	$this->addColumn('created_time', array(
            'header'    => Mage::helper('cms')->__('Date Created'),
            'index'     => 'created_time',
			'width'     => '150px',
            'type'      => 'datetime',
        ));	
		 $this->addColumn('update_time', array(
            'header'    => Mage::helper('cms')->__('Last Modified'),
            'index'     => 'update_time',
			'width'     => '150px',
            'type'      => 'datetime',
        ));	
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('testimonials')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('testimonials')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
	  
      return parent::_prepareColumns();
  }
	
	/*
	protected function _afterToHtml($html)
	{
		return parent::_afterToHtml($html). $this->_appendHtml();
	}
	*/
	private function _appendHtml()
    {
    	$html=
		'
		<style type="text/css">
		<!--
		#jextn-href { text-align:right; font-size:9px; }
		#jextn-href a{ text-decoration:none; color:#2F2F2F; }
    	#jextn-href a:hover { text-decoration:none;  }
		-->
		</style>
		<div id="jextn-href">Community version of Testimonials - Jextn <a href="'.$this->_jextnUrl.'" title="Magento Themes" target="_blank">Magento Themes</a></div>
		';
    return $html;
    }
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('testimonials_id');
        $this->getMassactionBlock()->setFormFieldName('testimonials');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('testimonials')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('testimonials')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('testimonials/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('testimonials')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('testimonials')->__('Status'),
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