<?php
/**
 * Author : Ebizmarts <info@ebizmarts.com>
 * Date   : 5/7/13
 * Time   : 11:08 PM
 * File   : Grid.php
 * Module : Ebizmarts_Magemonkey
 */
class Ebizmarts_AbandonedCart_Block_Adminhtml_Abandonedmails_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('id');
        $this->setId('ebizmarts_abandonedcart_abandonedmails_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _getCollectionClass()
    {
        return 'ebizmarts_abandonedcart/mailssent_collection';
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('reports/quote_collection');
		$collection->addFieldToFilter('items_count', array('neq' => '0'))
				   ->addFieldToFilter('main_table.is_active', '1')
				   ->addSubtotal()
				   ->setOrder('updated_at');
		$collection->addFieldToFilter('main_table.customer_email', array('neq' => ''));
		
		$this->setCollection($collection);
		
        return parent::_prepareCollection();
    }
    protected function _prepareColumns()
    {

        $this->addColumn('store', array(
            'header' => Mage::helper('ebizmarts_abandonedcart')->__('Store'),
            'type' => 'store',
            'index' => 'store_id'
        ));

        $this->addColumn('entity_id', array(
            'header' => Mage::helper('ebizmarts_abandonedcart')->__('Quote ID'),
            'index' => 'entity_id',
            'filter_index' => 'entity_id',
            'width' => '100px',
        ));

        $this->addColumn('customer_email', array(
            'header' => Mage::helper('ebizmarts_abandonedcart')->__('Customer Email'),
            'index' => 'customer_email',
        ));

        $this->addColumn('customer_firstname', array(
            'header' => Mage::helper('ebizmarts_abandonedcart')->__('Customer Name'),
            'index' => 'customer_firstname',
        ));

        $this->addColumn('coupon', array(
            'header' => Mage::helper('ebizmarts_abandonedcart')->__('Coupon #'),
            'index' => 'coupon_code',
        ));
        $this->addColumn('ebizmarts_abandonedcart_counter', array(
            'header' => Mage::helper('ebizmarts_abandonedcart')->__('Number of Mails Sent.'),
            'type' => 'text',
            'index' => 'ebizmarts_abandonedcart_counter',
        ));
       
        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    protected function getMailTypeOptions()
    {
        return array('abandoned cart'=>'abandoned cart','happy birthday'=>'happy birthday','new order'=>'new order', 'related products'=>'related products', 'product review'=>'product review', 'no activity'=>'no activity', 'wishlist'=>'wishlist');
    }

}