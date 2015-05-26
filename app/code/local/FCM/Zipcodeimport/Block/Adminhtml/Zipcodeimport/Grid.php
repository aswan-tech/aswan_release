<?php

class FCM_Zipcodeimport_Block_Adminhtml_Zipcodeimport_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('zipcodeimportGrid');
        $this->setDefaultSort('zipcodeimport_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('zipcodeimport/zipcodeimport')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('zipcodeimport_id', array(
            'header' => Mage::helper('zipcodeimport')->__('ID'),
            'align' => 'right',
            'width' => '5px',
            'index' => 'zipcodeimport_id',
        ));

        $this->addColumn('zip_code', array(
            'header' => Mage::helper('zipcodeimport')->__('Zip Code'),
            'align' => 'left',
            'width' => '20px',
            'index' => 'zip_code',
        ));

        $this->addColumn('state', array(
            'header' => Mage::helper('zipcodeimport')->__('State'),
            'align' => 'left',
            'width' => '100px',
            'index' => 'state',
        ));

        $this->addColumn('city', array(
            'header' => Mage::helper('zipcodeimport')->__('City'),
            'align' => 'left',
            'width' => '100px',
            'index' => 'city',
        ));

        $this->addColumn('express', array(
            'header' => Mage::helper('zipcodeimport')->__('Express'),
            'align' => 'left',
            'width' => '10px',
            'index' => 'express',
        ));

        $this->addColumn('standard', array(
            'header' => Mage::helper('zipcodeimport')->__('Standard'),
            'align' => 'left',
            'width' => '10px',
            'index' => 'standard',
        ));
        
        $this->addColumn('appointment', array(
            'header' => Mage::helper('zipcodeimport')->__('Appointment'),
            'align' => 'left',
            'width' => '10px',
            'index' => 'appointment',
        ));

        $this->addColumn('overnite', array(
            'header' => Mage::helper('zipcodeimport')->__('Overnite'),
            'align' => 'left',
            'width' => '10px',
            'index' => 'overnite',
        ));

        $this->addColumn('cod', array(
            'header' => Mage::helper('zipcodeimport')->__('COD'),
            'align' => 'left',
            'width' => '10px',
            'index' => 'cod',
        ));
        
        $this->addColumn('action',
                array(
                    'header' => Mage::helper('zipcodeimport')->__('Action'),
                    'width' => '50',
                    'type' => 'action',
                    'getter' => 'getId',
                    'actions' => array(
                        array(
                            'caption' => Mage::helper('zipcodeimport')->__('Edit'),
                            'url' => array('base' => '*/*/edit'),
                            'field' => 'id'
                        )
                    ),
                    'filter' => false,
                    'sortable' => false,
                    'index' => 'stores',
                    'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('zipcodeimport')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('zipcodeimport')->__('XML'));

        return parent::_prepareColumns();
    }

//    protected function _prepareMassaction() {
//        $this->setMassactionIdField('zipcodeimport_id');
//        $this->getMassactionBlock()->setFormFieldName('zipcodeimport');
//
//        $this->getMassactionBlock()->addItem('delete', array(
//            'label' => Mage::helper('zipcodeimport')->__('Delete'),
//            'url' => $this->getUrl('*/*/massDelete'),
//            'confirm' => Mage::helper('zipcodeimport')->__('Are you sure?')
//        ));
//
//        $statuses = Mage::getSingleton('zipcodeimport/status')->getOptionArray();
//
//        array_unshift($statuses, array('label' => '', 'value' => ''));
//        $this->getMassactionBlock()->addItem('status', array(
//            'label' => Mage::helper('zipcodeimport')->__('Change status'),
//            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
//            'additional' => array(
//                'visibility' => array(
//                    'name' => 'status',
//                    'type' => 'select',
//                    'class' => 'required-entry',
//                    'label' => Mage::helper('zipcodeimport')->__('Status'),
//                    'values' => $statuses
//                )
//            )
//        ));
//        return $this;
//    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}