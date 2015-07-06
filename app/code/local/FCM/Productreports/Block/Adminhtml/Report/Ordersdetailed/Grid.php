<?php

class FCM_Productreports_Block_Adminhtml_Report_Ordersdetailed_Grid extends FCM_Productreports_Block_Adminhtml_Report_Grid {

    public function __construct() {
        parent::__construct();
    }

    protected function _prepareCollection() {

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
        } else if (0 !== sizeof($this->_defaultFilter)) {
            $this->_setFilterValues($this->_defaultFilter);
        }

        $filterFrom = $this->getFilter('from');
        $filterTo = $this->getFilter('to');
	$filterSku = $this->getFilter('sku');        
	$filterCustomerEmail = $this->getFilter('customer_email');
	$filterCouponCode = $this->getFilter('coupon_code');
        $filterCustomerPostcode = $this->getFilter('customer_postcode');
        $filterCategory = $this->getFilter('product_category');
        $filterSubCategory = $this->getFilter('product_sub_category');
        $filterPaymentMethod = $this->getFilter('payment_method');
        $filterPaymentGateway = $this->getFilter('payment_gateway');
        $filterDcStatus = $this->getFilter('dc_status');
        $filterLatestStatus = $this->getFilter('latest_status');
        $filterCourier = $this->getFilter('courier');
		$product_type = $this->getFilter('product_type');
        
		if(empty($product_type)) { $product_type = 'simple'; }
		$product_name = $this->getFilter('product_name');
		$order_id = $this->getFilter('order_id');
		
        $_collection = Mage::getResourceModel('sales/order_collection');

        $orderJoinCondition = array(
            'order.entity_id = order_items.order_id'
        );

        $addressJoinCondition = array(
            'a.entity_id = order.billing_address_id',
            'a.address_type = "billing"'
        );

        $addressJoinConditionShipping = array(
            'b.entity_id = order.shipping_address_id',
            'b.address_type = "shipping"'
        );

        $shipmentTrackCondition = array(
            'shipment_track.order_id = order.entity_id'
        );

        $paymentCondition = array(
            'payment.parent_id = order.entity_id'
        );

        $categoryCondition = array(
            'category_product.product_id = order_items.product_id'
        );

        $_collection->getSelect()->reset()
                ->from(
                        array('order' => 'sales_flat_order'),
                        array(
                            'order_increment_id' => 'order.increment_id',
			    'customer_balance_amount' => 'order.customer_balance_amount',
                            'latest_status' => 'order.status',
                            'dc_status' => 'order.sent_to_erp',
                            'coupon_code' => 'order.coupon_code',
			    'coupon_rule_name' => 'order.coupon_rule_name',
			    'customer_id' => 'order.customer_id', 
                            'grand_total' => 'order.grand_total',
                            'customer_name' => "CONCAT(COALESCE(order.customer_firstname, ''), ' ', COALESCE(order.customer_lastname, ''))",
                            'customer_email' => "order.customer_email",
                            'gift_cards_amount' => 'order.gift_cards_amount',
                            'source' => 'order.source',
                            'campaign' => 'order.campaign',
                        )
                )
                ->joinRight(
                        array('order_items' => 'sales_flat_order_item'),
                        implode(' AND ', $orderJoinCondition),
                        array(
                            'item_name' => 'order_items.name',
                            'sku' => 'order_items.sku',
                            'created_at' => 'order_items.created_at',
                            'qty' => 'order_items.qty_ordered',
                            'item_id' => 'order_items.item_id',
                ))

            ->joinLeft(
                        array('shipment_track' => 'sales_flat_shipment_track'),
                        implode(' AND ', $shipmentTrackCondition),
                        array(
                            'courier' => "shipment_track.title",
                            'awb_no' => "shipment_track.track_number",
                            'shipment_date' => "shipment_track.created_at"
                        ),
                        array())
                ->joinLeft(
                        array('payment' => 'sales_flat_order_payment'),
                        implode(' AND ', $paymentCondition),
                        array(
                            'payment_gateway' => "payment.method",
                        ),
                        array())
                ->joinLeft(
                        array('category_product' => 'catalog_category_product'),
                        implode(' AND ', $categoryCondition),
                        array(),
                        array());

		if( $product_type == 'all' ) {
			$_collection->addAttributeToFilter('product_type', array('IN'=>array('simple', 'giftcard')));
		}
		else{
			$_collection->addAttributeToFilter('product_type', $product_type);
		}
        
        if(!empty($product_name)) {
			$_collection->addAttributeToFilter('name', array('like' => '%'.$product_name.'%'));
		}
		
		if(!empty($order_id)) {
			$_collection->addAttributeToFilter('order.increment_id', array('eq' => $order_id));
		}
		
        if (!empty($filterFrom)) {
            list($dd, $mm, $yyyy) = explode("-", $filterFrom);
            $time = mktime('00', '00', '00', $mm, $dd, $yyyy);
            $dateFrom = date('Y-m-d H:i:s', $time - (60 * 60 * 5) - (60 * 30));
            
            //$timestamp = strtotime($filterFrom);
            //$timestamp = Mage::getModel('core/date')->gmtDate($timestamp);
            //$dateFrom = date("Y-m-d H:i:s", $timestamp);

            $_collection->addAttributeToFilter('order.created_at', array('gteq' => $dateFrom));
        }

        if (!empty($filterTo)) {
            list($dd, $mm, $yyyy) = explode("-", $filterTo);
            $time = mktime('23', '59', '59', $mm, $dd, $yyyy);
            $dateTo = date('Y-m-d H:i:s', $time - (60 * 60 * 5) - (60 * 30));
            
            //$timestamp = strtotime($filterTo);
            //$timestamp = mktime('23', '59', '59', date("m", $timestamp), date("d", $timestamp), date("Y", $timestamp));
            //$timestamp = Mage::getModel('core/date')->gmtDate($timestamp);
            //$dateTo = date("Y-m-d H:i:s", $timestamp);

            $_collection->addAttributeToFilter('order.created_at', array('lteq' => $dateTo));
        }
		
		if (!empty($filterSku)) {
            $_collection->addAttributeToFilter('sku', $filterSku);
        }				
		
        if (!empty($filterCustomerEmail)) {
            $_collection->addAttributeToFilter('customer_email', $filterCustomerEmail);
        }
		
		if (!empty($filterCouponCode)) {
            $_collection->addAttributeToFilter('coupon_code', $filterCouponCode);
        }
		
        if (!empty($filterCustomerPostcode)) {
            $_collection->addAttributeToFilter('a.postcode', $filterCustomerPostcode);
        }

        if (!empty($filterSubCategory)) {
            $_collection->addAttributeToFilter('category_product.category_id', $filterSubCategory);
        } elseif (!empty($filterCategory)) {
            $_collection->addAttributeToFilter('category_product.category_id', $filterCategory);
        }

        if (!empty($filterPaymentMethod)) {

            if ($filterPaymentMethod == 'postpaid') {
                $_collection->addAttributeToFilter('payment.method', 'cashondelivery');
            } else {
                $_collection->addAttributeToFilter('payment.method', array('neq' => 'cashondelivery'));
            }
        }

        if (!empty($filterPaymentGateway)) {
            $_collection->addAttributeToFilter('payment.method', array('eq' => $filterPaymentGateway));
        }

        if ($filterDcStatus != "" && $filterDcStatus != '-1') {
            $_collection->addAttributeToFilter('order.sent_to_erp', $filterDcStatus);
        }

        if ($filterLatestStatus != "" && $filterLatestStatus != '0') {
            $_collection->addAttributeToFilter('order.status', $filterLatestStatus);
        }

        if (!empty($filterCourier) && $filterCourier != '0') {
            $_collection->addAttributeToFilter('shipment_track.title', $filterCourier);
        }

        $_collection->getSelect()->group('order_items.item_id');

        //Mage::log('SQL: ' . $_collection->getSelect()->__toString());

        $this->setCollection($_collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        //$currencyCode = $this->getCurrentCurrencyCode();

        $this->addColumn('created_at', array(
            'header' => Mage::helper('productreports')->__('Order Date'),
            'index' => 'created_at',
            'sortable' => false,
            'renderer' => 'FCM_Productreports_Block_Adminhtml_Report_Renderer_Date',
            'type' => 'text',
        ));

        $this->addColumn('order_time', array(
            'header' => Mage::helper('productreports')->__('Order Time'),
            'index' => 'created_at',
            'sortable' => false,
            'renderer' => 'FCM_Productreports_Block_Adminhtml_Report_Renderer_Time',
            'type' => 'text',
        ));

        $this->addColumn('order_increment_id', array(
            'header' => Mage::helper('productreports')->__('Order Number'),
            'index' => 'order_increment_id',
            'sortable' => false,
            'type' => 'text',
        ));

        $this->addColumn('customer_id', array(
            'header' => Mage::helper('productreports')->__('Customer ID'),
            'index' => 'customer_id',
            'sortable' => false,
            //'renderer' => 'FCM_Productreports_Block_Adminhtml_Report_Renderer_Custid',
        ));

        $this->addColumn('source', array(
            'header' => Mage::helper('productreports')->__('Source'),
            'index' => 'source',
            'sortable' => false,
        ));

        $this->addColumn('campaign', array(
            'header' => Mage::helper('productreports')->__('Campaign'),
            'index' => 'campaign',
            'sortable' => false,
        ));

        $this->addColumn('customer_email', array(
            'header' => Mage::helper('productreports')->__('Customer Email'),
            'index' => 'customer_email',
            'sortable' => false
        ));

        $this->addColumn('payment_method', array(
            'header' => Mage::helper('productreports')->__('Payment Method'),
            'index' => 'payment_gateway',
            'sortable' => false,
            'renderer' => 'FCM_Productreports_Block_Adminhtml_Report_Renderer_Pmethod',
        ));

        $this->addColumn('payment_gateway', array(
            'header' => Mage::helper('productreports')->__('Payment Gateway'),
            'index' => 'payment_gateway',
            'sortable' => false
        ));

        $this->addColumn('dc_status', array(
            'header' => Mage::helper('productreports')->__('DC Status'),
            'index' => 'dc_status',
            'sortable' => false,
            'renderer' => 'FCM_Productreports_Block_Adminhtml_Report_Renderer_Dcstatus',
        ));

        $this->addColumn('latest_status', array(
            'header' => Mage::helper('productreports')->__('Latest Status'),
            'index' => 'latest_status',
            'sortable' => false,
            'renderer' => 'FCM_Productreports_Block_Adminhtml_Report_Renderer_Lstatus',
        ));

        $this->addColumn('sku', array(
            'header' => Mage::helper('productreports')->__('Item No'),
            'index' => 'sku',
            'sortable' => false
        ));
		
		$this->addColumn('ean', array(
            'header' => Mage::helper('productreports')->__('EAN'),
            'index' => 'sku',
            'sortable' => false,
            'renderer' => 'FCM_Productreports_Block_Adminhtml_Report_Renderer_Ean',
        ));

        $this->addColumn('qty', array(
            'header' => Mage::helper('productreports')->__('Qty'),
            'index' => 'qty',
            'sortable' => false,
            'type' => 'number'
        ));

        $this->addColumn('product_mrp', array(
            'header' => Mage::helper('productreports')->__('Item MRP'),
            'index' => 'item_id',
            'sortable' => false,
            'renderer' => 'FCM_Productreports_Block_Adminhtml_Report_Renderer_Mrp',            
        ));

        $this->addColumn('original_price', array(
            'header' => Mage::helper('productreports')->__('Special Price'),
            'index' => 'item_id',
            'sortable' => false,
            'renderer' => 'FCM_Productreports_Block_Adminhtml_Report_Renderer_Price',
        ));

        $this->addColumn('discount_percent', array(
            'header' => Mage::helper('productreports')->__('Discount %'),
            'index' => 'item_id',
            'sortable' => false,
            'renderer' => 'FCM_Productreports_Block_Adminhtml_Report_Renderer_Dpercent',
        ));

        $this->addColumn('discount_amount', array(
            'header' => Mage::helper('productreports')->__('Discount Value'),
            'index' => 'item_id',
            'sortable' => false,
            'renderer' => 'FCM_Productreports_Block_Adminhtml_Report_Renderer_Damount',
        ));

        $this->addColumn('tax_percent', array(
            'header' => Mage::helper('productreports')->__('Tax %'),
            'index' => 'item_id',
            'sortable' => false,
            'renderer' => 'FCM_Productreports_Block_Adminhtml_Report_Renderer_Tpercent',
        ));
        $this->addColumn('tax_amount', array(
            'header' => Mage::helper('productreports')->__('Tax Value'),
            'index' => 'item_id',
            'sortable' => false,
            'renderer' => 'FCM_Productreports_Block_Adminhtml_Report_Renderer_Tamount',
        ));

        $this->addColumn('coupon_code', array(
            'header' => Mage::helper('productreports')->__('Coupon Code'),
            'index' => 'coupon_code',
            'sortable' => false
        ));
		
	$this->addColumn('coupon_rule_name', array(
            'header'    => Mage::helper('productreports')->__('Shopping Cart Price Rule'),
            'sortable'  => false,
            'index'     => 'coupon_rule_name'
        ));

        $this->addColumn('coupon_percent', array(
            'header' => Mage::helper('productreports')->__('Coupon %'),
            'index' => 'item_id',
            'sortable' => false,
            'renderer' => 'FCM_Productreports_Block_Adminhtml_Report_Renderer_Cpercent',
        ));
		
	$this->addColumn('customer_balance_amount', array(
            'header' => Mage::helper('productreports')->__('Store Credit'),
            'index' => 'customer_balance_amount',
            'sortable' => false,
			//'currency_code' => $currencyCode,
            'type' => 'currency',
        ));

        $this->addColumn('coupon_amount', array(
            'header' => Mage::helper('productreports')->__('Coupon Money'),
            'index' => 'item_id',
            'sortable' => false,
            'renderer' => 'FCM_Productreports_Block_Adminhtml_Report_Renderer_Camount',
        ));

        $this->addColumn('row_total', array(
            'header' => Mage::helper('productreports')->__('Amount to Customer'),
            'index' => 'item_id',
            'sortable' => false,
            'renderer' => 'FCM_Productreports_Block_Adminhtml_Report_Renderer_Rtotal',
        ));

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('productreports')->__('Order Value'),
            'index' => 'grand_total',
            'sortable' => false,
            //'currency_code' => $currencyCode,
            'type' => 'currency',
        ));

        $this->addColumn('shipment_date', array(
            'header' => Mage::helper('productreports')->__('Shipment Date'),
            'index' => 'shipment_date',
            'sortable' => false
        ));

        $this->addColumn('courier', array(
            'header' => Mage::helper('productreports')->__('Courier'),
            'index' => 'courier',
            'sortable' => false
        ));

        $this->addColumn('awb_no', array(
            'header' => Mage::helper('productreports')->__('AWB No.'),
            'index' => 'awb_no',
            'sortable' => false
        ));
        
        $this->addColumn('gvcode', array(
            'header' => Mage::helper('productreports')->__('GV Code'),
            'index' => 'order_increment_id',
            'sortable' => false,
            'renderer' => 'FCM_Productreports_Block_Adminhtml_Report_Renderer_Giftcard',
        ));
        
        $this->addColumn('gv_value', array(
            'header' => Mage::helper('productreports')->__('GV Value.'),
            'index' => 'order_increment_id',
            'sortable' => false,
            'renderer' => 'FCM_Productreports_Block_Adminhtml_Report_Renderer_Giftcardvalue',
        ));

        $this->addExportType('*/*/exportWorkflowCsv', Mage::helper('productreports')->__('CSV'));
        $this->addExportType('*/*/exportWorkflowXml', Mage::helper('productreports')->__('Excel XML'));

        return parent::_prepareColumns();
    }

}
