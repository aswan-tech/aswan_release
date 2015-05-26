<?php

/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-ENTERPRISE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento ENTERPRISE edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento ENTERPRISE edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Blog
 * @version    1.1.1
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-ENTERPRISE.txt
 */
class MLogix_Gallery_Block_Manage_Comment_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {

        parent::__construct();
        $this->setId('commentGrid');
        $this->setDefaultSort('main_table.status');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {

        $collection = Mage::getModel('gallery/comment')->getCollection();
        $collection->getSelect()->joinLeft(array('gallery_main' => $collection->getTable('gallery/week')), 'main_table.post_id=gallery_main.gallery_id', array('gallery_main.item_title'));

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $this->addColumn('comment_id', array(
            'header' => Mage::helper('gallery')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'comment_id',
        ));

        $this->addColumn('comment', array(
            'header' => Mage::helper('gallery')->__('Comment'),
            'align' => 'left',
            'index' => 'comment',
        ));

        $this->addColumn('user', array(
            'header' => Mage::helper('gallery')->__('Poster'),
            'width' => '150px',
            'index' => 'user',
        ));

        $this->addColumn('email', array(
            'header' => Mage::helper('gallery')->__('Email Address'),
            'width' => '150px',
            'index' => 'email',
        ));

        $this->addColumn('created_time', array(
            'header' => Mage::helper('gallery')->__('Created'),
            'align' => 'center',
            'width' => '120px',
            'type' => 'date',
            'default' => '--',
            'index' => 'created_time',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('gallery')->__('Status'),
            'align' => 'center',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'filter_index' => 'main_table.status',
            'options' => array(
                1 => 'Unapproved',
                2 => 'Approved',
            ),
        ));

        $this->addColumn('item_title', array(
            'header' => Mage::helper('gallery')->__('Look Title'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'item_title',
            'type' => 'text'
        ));

        $this->addColumn('gallery_main.gallery_id', array(
            'header' => Mage::helper('gallery')->__('Link to Look'),
            'width' => '50px',
            'type' => 'action',
            'getter' => 'getPostId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('gallery')->__('View'),
                    'url' => array(
                        'base' => '*/adminhtml_week/edit'
                    ),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('gallery')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('gallery')->__('Approve'),
                    'url' => array('base' => '*/*/approve'),
                    'field' => 'id'
                ),
                array(
                    'caption' => Mage::helper('gallery')->__('Unapprove'),
                    'url' => array('base' => '*/*/unapprove'),
                    'field' => 'id'
                ),
                array(
                    'caption' => Mage::helper('gallery')->__('Delete'),
                    'url' => array('base' => '*/*/delete'),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {

        $this->setMassactionIdField('main_table.comment_id');
        $this->getMassactionBlock()->setFormFieldName('comment');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('gallery')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('gallery')->__('Are you sure?')
        ));

        $this->getMassactionBlock()->addItem('approve', array(
            'label' => Mage::helper('gallery')->__('Approve'),
            'url' => $this->getUrl('*/*/massApprove'),
            'confirm' => Mage::helper('gallery')->__('Are you sure?')
        ));

        $this->getMassactionBlock()->addItem('unapprove', array(
            'label' => Mage::helper('gallery')->__('Unapprove'),
            'url' => $this->getUrl('*/*/massUnapprove'),
            'confirm' => Mage::helper('gallery')->__('Are you sure?')
        ));
        return $this;
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
