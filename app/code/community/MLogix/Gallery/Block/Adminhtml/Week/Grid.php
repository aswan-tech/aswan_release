<?php

/**
 * Magic Logix Gallery
 *
 * Provides an image gallery extension for Magento
 *
 * @category		MLogix
 * @package		Gallery
 * @author		Brady Matthews
 * @copyright		Copyright (c) 2008 - 2010, Magic Logix, Inc.
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/us/
 * @link		http://www.magiclogix.com
 * @link		http://www.magentoadvisor.com
 * @since		Version 1.0
 *
 * Please feel free to modify or distribute this as you like,
 * so long as it's for noncommercial purposes and any
 * copies or modifications keep this comment block intact
 *
 * If you would like to use this for commercial purposes,
 * please contact me at brady@magiclogix.com
 *
 * For any feedback, comments, or questions, please post
 * it on my blog at http://www.magentoadvisor.com/plugins/gallery/
 *
 */
?><?php

class MLogix_Gallery_Block_Adminhtml_Week_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('weekGrid');
        $this->setDefaultSort('gallery_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(false);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('gallery/week')->getCollection(); 
		
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('gallery_id', array(
            'header' => Mage::helper('gallery')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'gallery_id',
        ));

        $this->addColumn('item_title', array(
            'header' => Mage::helper('gallery')->__('Title'),
            'align' => 'left',
            'index' => 'item_title',
        ));
		
		$this->addColumn('heading', array(
            'header' => Mage::helper('gallery')->__('Heading'),
            'align' => 'left',
            'index' => 'heading',
        ));
/*
        $this->addColumn('width', array(
            'header' => Mage::helper('gallery')->__('Width'),
            'align' => 'left',
            'index' => 'width',
        ));
        $this->addColumn('height', array(
            'header' => Mage::helper('gallery')->__('Height'),
            'align' => 'left',
            'index' => 'height',
        ));
*/
        $this->addColumn('position_no', array(
            'header' => Mage::helper('gallery')->__('Position'),
            'width' => '150px',
            'index' => 'position_no',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('gallery')->__('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                1 => 'Enabled',
                2 => 'Disabled',
            ),
        ));

        $this->addColumn('action',
                array(
                    'header' => Mage::helper('gallery')->__('Action'),
                    'width' => '100',
                    'type' => 'action',
                    'getter' => 'getId',
                    'actions' => array(
                        array(
                            'caption' => Mage::helper('gallery')->__('Edit'),
                            'url' => array('base' => '*/*/edit'),
                            'field' => 'id'
                        )
                    ),
                    'filter' => false,
                    'sortable' => false,
                    'index' => 'stores',
                    'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('gallery')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('gallery')->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('gallery_id');
        $this->getMassactionBlock()->setFormFieldName('week');

        //$this->getMassactionBlock()->addItem('delete', array(
            //'label' => Mage::helper('gallery')->__('Delete'),
            //'url' => $this->getUrl('*/*/massDelete'),
            //'confirm' => Mage::helper('gallery')->__('Are you sure?')
        //));

        $statuses = Mage::getSingleton('gallery/status')->getOptionArray();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('gallery')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('gallery')->__('Status'),
                    'values' => $statuses
                )
            )
        ));
        return $this;
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}