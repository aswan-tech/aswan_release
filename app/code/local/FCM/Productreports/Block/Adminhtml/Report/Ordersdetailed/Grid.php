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
        var_dump($product_type);
		if(empty($product_type)) { $product_type = 'configurable'; }
		$product_name = $this->getFilter('product_name');
		$order_id = $this->getFilter('order_id');
		
        $_collection = Mage::getResourceModel('sales/order_collection');

        $orderJoinCondition = array(
            "(sfo.entity_id = order_items.order_id)"
        );

        $addressJoinCondition = array(
            'a.entity_id = sfo.billing_address_id',
            'a.address_type = "billing"'
        );

        $addressJoinConditionShipping = array(
            'b.entity_id = sfo.shipping_address_id',
            'b.address_type = "shipping"'
        );

        $shipmentTrackCondition = array(
            'shipment_track.order_id = sfo.entity_id'
        );

        $paymentCondition = array(
            'payment.parent_id = sfo.entity_id'
        );

        $categoryCondition = array(
            'category_product.product_id = order_items.product_id'
        );

        $_collection->getSelect()->reset()
                ->from(
                        array('sfo' => 'sales_flat_order'),
                        array(
                            'order_increment_id' => 'sfo.increment_id',
							'customer_balance_amount' => 'sfo.customer_balance_amount',
                            'latest_status' => 'sfo.status',
                            'dc_status' => 'sfo.sent_to_erp',
                            'coupon_code' => 'sfo.coupon_code',
							'coupon_rule_name' => 'sfo.coupon_rule_name',
							'customer_id' => 'sfo.customer_id', 
                            'grand_total' => 'sfo.grand_total',
                           // 'customer_name' => "CONCAT(COALESCE(sfo.customer_firstname, ''), ' ', COALESCE(sfo.customer_lastname, ''))",
                            'customer_email' => "sfo.customer_email",
                            'gift_cards_amount' => 'sfo.gift_cards_amount',
                            'source' => 'sfo.source',
                            'campaign' => 'sfo.campaign',
                        )
                )
                ->joinRight(
                        array('order_items' => 'sales_flat_order_item'),
                        implode(' AND ', $orderJoinCondition),
                        array(
                            'item_name' => 'order_items.name',
                            'sku' => 'order_items.sku',
                            'product_mrp' => 'order_items.product_mrp',
                            'original_price' => 'order_items.original_price',
                            //'catalog_discount_percentage' => '(((order_items.product_mrp-order_items.original_price)/order_items.product_mrp)*100)',
                            'coupon_discount_percentage' => 'order_items.discount_percent',
                           // 'discount_amount' => '(order_items.product_mrp - order_items.original_price)',
                            'coupon_discount_amount' => 'order_items.discount_amount',
                            'tax_percent' => 'order_items.tax_percent',
                            'tax_amount' => 'order_items.tax_amount',
                            'row_total' => '((order_items.row_total+order_items.tax_amount+order_items.hidden_tax_amount+order_items.weee_tax_applied_row_amount)-order_items.discount_amount)',
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
                        array());
               /* ->joinLeft(
                        array('category_product' => 'catalog_category_product'),
                        implode(' AND ', $categoryCondition),
                        array(),
                        array());
				*/
		if( $product_type == 'all' ) {
			$_collection->addAttributeToFilter('order_items.product_type', array('IN'=>array('configurable', 'giftcard')));
		}
		else{
			$_collection->addAttributeToFilter('order_items.product_type', $product_type);
		}
        
        if(!empty($product_name)) {
			$_collection->addAttributeToFilter('name', array('like' => '%'.$product_name.'%'));
		}
		
		if(!empty($order_id)) {
			$_collection->addAttributeToFilter('sfo.increment_id', array('eq' => $order_id));
		}
		
        if (!empty($filterFrom)) {
            list($dd, $mm, $yyyy) = explode("-", $filterFrom);
            $time = mktime('00', '00', '00', $mm, $dd, $yyyy);
            $dateFrom = date('Y-m-d H:i:s', $time - (60 * 60 * 5) - (60 * 30));
            
     
            $_collection->addAttributeToFilter('sfo.created_at', array('gteq' => $dateFrom));
        }

        if (!empty($filterTo)) {
            list($dd, $mm, $yyyy) = explode("-", $filterTo);
            $time = mktime('23', '59', '59', $mm, $dd, $yyyy);
            $dateTo = date('Y-m-d H:i:s', $time - (60 * 60 * 5) - (60 * 30));
     
            $_collection->addAttributeToFilter('sfo.created_at', array('lteq' => $dateTo));
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
            $_collection->addAttributeToFilter('sfo.sent_to_erp', $filterDcStatus);
        }

        if ($filterLatestStatus != "" && $filterLatestStatus != '0') {
            $_collection->addAttributeToFilter('sfo.status', $filterLatestStatus);
        }

        if (!empty($filterCourier) && $filterCourier != '0') {
            $_collection->addAttributeToFilter('shipment_track.title', $filterCourier);
        }

        #$_collection->getSelect()->group('order_items.item_id');
        $_collection->getSelect()->order('sfo.entity_id desc');
        #echo $_collection->getSelect()->__toString();
        //Mage::log('SQL: ' . $_collection->getSelect()->__toString());

        $this->setCollection($_collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $this->addColumn('created_at', array(
            'header' => Mage::helper('productreports')->__('Order Date'),
            'index' => 'created_at',
            'sortable' => false,
            'type' => 'text',
        ));

        $this->addColumn('order_increment_id', array(
            'header' => Mage::helper('productreports')->__('Order Number'),
            'index' => 'order_increment_id',
            'sortable' => false,
            'type' => 'text',
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
            'index' => 'product_mrp',
            'sortable' => false,  
        ));

        $this->addColumn('original_price', array(
            'header' => Mage::helper('productreports')->__('Special Price'),
            'index' => 'original_price',
            'sortable' => false,
            //'renderer' => 'FCM_Productreports_Block_Adminhtml_Report_Renderer_Price',
        ));

        /*$this->addColumn('discount_percent', array(
            'header' => Mage::helper('productreports')->__('Discount %'),
            'index' => 'catalog_discount_percentage',
            'sortable' => false,
            //'renderer' => 'FCM_Productreports_Block_Adminhtml_Report_Renderer_Dpercent',
        ));
		
        $this->addColumn('discount_amount', array(
            'header' => Mage::helper('productreports')->__('Discount Value'),
            'index' => 'item_id',
            'sortable' => false,
            'renderer' => 'FCM_Productreports_Block_Adminhtml_Report_Renderer_Damount',
        ));
		*/
        $this->addColumn('tax_percent', array(
            'header' => Mage::helper('productreports')->__('Tax %'),
            'index' => 'tax_percent',
            'sortable' => false,
           // 'renderer' => 'FCM_Productreports_Block_Adminhtml_Report_Renderer_Tpercent',
        ));
        $this->addColumn('tax_amount', array(
            'header' => Mage::helper('productreports')->__('Tax Value'),
            'index' => 'tax_amount',
            'sortable' => false,
            //'renderer' => 'FCM_Productreports_Block_Adminhtml_Report_Renderer_Tamount',
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
            'index' => 'coupon_discount_percentage',
            'sortable' => false,
            //'renderer' => 'FCM_Productreports_Block_Adminhtml_Report_Renderer_Cpercent',
        ));
		
	$this->addColumn('customer_balance_amount', array(
            'header' => Mage::helper('productreports')->__('Store Credit'),
            'index' => 'customer_balance_amount',
            'sortable' => false,
            'type' => 'currency',
        ));

        $this->addColumn('coupon_amount', array(
            'header' => Mage::helper('productreports')->__('Coupon Money'),
            'index' => 'coupon_discount_amount',
            'sortable' => false,
            //'renderer' => 'FCM_Productreports_Block_Adminhtml_Report_Renderer_Camount',
        ));

        $this->addColumn('row_total', array(
            'header' => Mage::helper('productreports')->__('Amount to Customer'),
            'index' => 'row_total',
            'sortable' => false,
            //'renderer' => 'FCM_Productreports_Block_Adminhtml_Report_Renderer_Rtotal',
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
