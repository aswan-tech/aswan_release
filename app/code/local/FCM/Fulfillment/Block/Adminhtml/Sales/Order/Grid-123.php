<?php
/**
 * Magento Block to extend the Magento orders grid
 *
 * This block overrides the core Magento Sales order grid block.
 * It adds a new column 'Sent to Warehouse' in Grid.
 *
 * @category    FCM
 * @package     FCM_Fulfillment
 * @author	Pawan Prakash Gupta
 * @author_id	51405591
 * @company	HCL Technologies
 * @created Thursday, June 28, 2012
 * @copyright	Four cross media
 */

/**
 * Extending Adminhtml sales orders grid
 *
 * @category   FCM
 * @package    FCM_Fulfillment
 * @author	   Pawan Prakash Gupta <51405591>
 */
 
class FCM_Fulfillment_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{
	public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {   
		$addressJoinCondition = array(
            'a.entity_id = ord.billing_address_id',
            'a.address_type = "billing"'
        );
		
        $collection = Mage::getResourceModel($this->_getCollectionClass());
		$joinTable = Mage::getSingleton('core/resource')->getTableName('sales/order');
		
		$collection->getSelect()->joinLeft(array('ord' => $joinTable), 'main_table.entity_id = ord.entity_id',  array('sent_to_erp','customer_email' ))
		->joinLeft(array('a' => 'sales_flat_order_address'),
                        implode(' AND ', $addressJoinCondition),
                        array('customer_phone' => "a.telephone"),
                        array());
						
        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    protected function _prepareColumns()
    {		
		$this->addColumn('real_order_id', array(
            'header'=> Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'increment_id',
			'filter_index' => 'main_table.increment_id',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('sales')->__('Purchased From (Store)'),
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view'=> true,
                'display_deleted' => true,
            ));
        }

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
			'filter_index' => 'main_table.created_at',
            'type' => 'datetime',
            'width' => '100px',
        ));

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'index' => 'billing_name',
			'filter_index' => 'main_table.billing_name',
        ));

        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'index' => 'shipping_name',
			'filter_index' => 'main_table.shipping_name',
        ));
		
		$this->addColumn('customer_email', array(
            'header' => Mage::helper('sales')->__('Customer Email'),
            'index' => 'customer_email',
			'filter_index' => 'ord.customer_email',
        ));
		
		$this->addColumn('customer_phone', array(
            'header' => Mage::helper('sales')->__('Customer Phone'),
            'index' => 'customer_phone',
            'filter_index' => 'a.telephone',
        ));

        $this->addColumn('base_grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Base)'),
            'index' => 'base_grand_total',
			'filter_index' => 'main_table.base_grand_total',
            'type'  => 'currency',
            'currency' => 'base_currency_code',
        ));

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
            'index' => 'grand_total',
			'filter_index' => 'main_table.grand_total',
            'type'  => 'currency',
            'currency' => 'order_currency_code',
        ));
		
		$dcstatus = Mage::getModel('fulfillment/process')->getDcStatuses();;
		
		$this->addColumnAfter('sent_to_erp', array(
            'header'=> Mage::helper('sales')->__('DC Status'),
            'width' => '80px',
            'type'  => 'options',
			'align' => 'center',
            'index' => 'sent_to_erp',
			'filter_index' => 'ord.sent_to_erp',
			'options'   => Mage::getModel('fulfillment/process')->getDcStatuses(),
        ), 'grand_total');

        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
			'filter_index' => 'main_table.status',
            'type'  => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            $this->addColumn('action',
                array(
                    'header'    => Mage::helper('sales')->__('Action'),
                    'width'     => '50px',
                    'type'      => 'action',
                    'getter'     => 'getId',
                    'actions'   => array(
                        array(
                            'caption' => Mage::helper('sales')->__('View'),
                            'url'     => array('base'=>'*/sales_order/view'),
                            'field'   => 'order_id'
                        )
                    ),
                    'filter'    => false,
                    'sortable'  => false,
                    'index'     => 'stores',
                    'is_system' => true,
            ));
        }
        $this->addRssList('rss/order/new', Mage::helper('sales')->__('New Order RSS'));

        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));

        return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
    }
	
	protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('order_ids');
        $this->getMassactionBlock()->setUseSelectAll(false);

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/cancel')) {
            $this->getMassactionBlock()->addItem('cancel_order', array(
                 'label'=> Mage::helper('sales')->__('Cancel'),
                 'url'  => $this->getUrl('*/sales_order/massCancel'),
            ));
        }

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/hold')) {
            $this->getMassactionBlock()->addItem('hold_order', array(
                 'label'=> Mage::helper('sales')->__('Hold'),
                 'url'  => $this->getUrl('*/sales_order/massHold'),
            ));
        }

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/unhold')) {
            $this->getMassactionBlock()->addItem('unhold_order', array(
                 'label'=> Mage::helper('sales')->__('Unhold'),
                 'url'  => $this->getUrl('*/sales_order/massUnhold'),
            ));
        }

        $this->getMassactionBlock()->addItem('pdfinvoices_order', array(
             'label'=> Mage::helper('sales')->__('Print Invoices'),
             'url'  => $this->getUrl('*/sales_order/pdfinvoices'),
        ));

        $this->getMassactionBlock()->addItem('pdfshipments_order', array(
             'label'=> Mage::helper('sales')->__('Print Packingslips'),
             'url'  => $this->getUrl('*/sales_order/pdfshipments'),
        ));

        $this->getMassactionBlock()->addItem('pdfcreditmemos_order', array(
             'label'=> Mage::helper('sales')->__('Print Credit Memos'),
             'url'  => $this->getUrl('*/sales_order/pdfcreditmemos'),
        ));

        $this->getMassactionBlock()->addItem('pdfdocs_order', array(
             'label'=> Mage::helper('sales')->__('Print All'),
             'url'  => $this->getUrl('*/sales_order/pdfdocs'),
        ));

        $this->getMassactionBlock()->addItem('print_shipping_label', array(
             'label'=> Mage::helper('sales')->__('Print Shipping Labels'),
             'url'  => $this->getUrl('*/sales_order_shipment/massPrintShippingLabel'),
        ));
		
		
		//$this->getMassactionBlock()->addItem('sent_to_dc', array(
		//	 'label'=> Mage::helper('sales')->__('Sent to DC'),
		//	 'url'  => Mage::helper('adminhtml')->getUrl('*/sales_order/sentToDc/'),
		//));
		
		$this->getMassactionBlock()->addItem('not_sent_to_dc', array(
			 'label'=> Mage::helper('sales')->__('Not Sent to DC'),
			 'url'  => Mage::helper('adminhtml')->getUrl('*/sales_order/notSentToDc/'),
		));

        return $this;
    }
}
