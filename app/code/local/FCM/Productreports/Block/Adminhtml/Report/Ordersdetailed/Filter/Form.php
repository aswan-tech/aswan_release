<?php

class FCM_Productreports_Block_Adminhtml_Report_Ordersdetailed_Filter_Form extends FCM_Productreports_Block_Adminhtml_Report_Filter_Form {

    /**
     * Add fieldset with general report fields
     *
     * @return Mage_Adminhtml_Block_Report_Filter_Form
     */
    protected function _prepareForm() {

        $actionUrl = $this->getUrl('*/*/detailedorders');

        $form = new Varien_Data_Form(
                        array('id' => 'filter_form', 'action' => $actionUrl, 'method' => 'get')
        );

        $htmlIdPrefix = 'order_detailed_';
        $form->setHtmlIdPrefix($htmlIdPrefix);
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('productreports')->__('Filter')));

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);

        $methods = $this->getActivPaymentMethods();
        $dcstatus = $this->getDcStatuses();

        $fieldset->addField('store_ids', 'hidden', array(
            'name' => 'store_ids'
        ));

        $fieldset->addField('from', 'date', array(
            'name' => 'from',
            'format' => $dateFormatIso,
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'label' => Mage::helper('productreports')->__('From'),
            'title' => Mage::helper('productreports')->__('From'),
        ));

        $fieldset->addField('to', 'date', array(
            'name' => 'to',
            'format' => $dateFormatIso,
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'label' => Mage::helper('productreports')->__('To'),
            'title' => Mage::helper('productreports')->__('To')
        ));

        $element = $fieldset->addField('product_category', 'select', array(
                    'name' => 'product_category',
                    'values' => Mage::helper('productreports')->getProductCategories("", "", "order_report"),
                    'label' => Mage::helper('productreports')->__('Department'),
                    'title' => Mage::helper('productreports')->__('Department'),
                    'options' => array(
                        '' => Mage::helper('productreports')->__('Select Department')
                    ),
                    'onchange' => 'reloadSubCategories(this)'
                ));

        $element->setAfterElementHtml("<script type=\"text/javascript\">
                function reloadSubCategories(selectElement){
                        var reloadurl = '" . $this->getUrl('*/*/reloadcategories') . "ctgid/' + selectElement.value;

                        new Ajax.Request(reloadurl, {
                                method: 'get',
                                onLoading: function (transport) {
                                        $('" . $htmlIdPrefix . "product_sub_category').update('Loading...');
                                },
                                onComplete: function(transport) {
                                        $('" . $htmlIdPrefix . "product_sub_category').update(transport.responseText);
                                }
                        });
                }
        </script>");

        $fieldset->addField('product_sub_category', 'select', array(
            'name' => 'product_sub_category',
            'label' => Mage::helper('productreports')->__('Category'),
            'title' => Mage::helper('productreports')->__('Category'),
            'options' => array(
                '' => Mage::helper('productreports')->__('Select Category')
            ),
        ));

        $fieldset->addField("payment_method", "select", array(
            "label" => Mage::helper("productreports")->__("Payment Method"),
            "name" => "payment_method",
            "values" => array(
                array(
                    "value" => '',
                    "label" => Mage::helper("productreports")->__("Select Payment Method"),
                ),
                array(
                    "value" => 'prepaid',
                    "label" => Mage::helper("productreports")->__("Prepaid"),
                ),
                array(
                    "value" => 'postpaid',
                    "label" => Mage::helper("productreports")->__("Postpaid"),
                ),
            ),
            "options" => array(
                '' => Mage::helper('productreports')->__('Select Payment Method')
            ),
        ));

        $fieldset->addField("payment_gateway", "select", array(
            "label" => Mage::helper("productreports")->__("Payment Gateway"),
            "name" => "payment_gateway",
            "values" => $methods,
            "options" => array(
                '' => Mage::helper('productreports')->__('Select Payment Gateway')
            ),
        ));


        $filter = $this->getRequest()->getParam('filter', null);

        if (is_string($filter)) {
            $data = array();
            $filter = base64_decode($filter);
            parse_str(urldecode($filter), $data);

            if ($data['dc_status'] != '-1') {
                $opt_val_ls = $data['dc_status'];
            } else {
                $opt_val_ls = "-1";
            }
        } else {
            $opt_val_ls = "-1";
        }

        $fieldset->addField("dc_status", "select", array(
            "label" => Mage::helper("productreports")->__("DC Status"),
            "name" => "dc_status",
            "options" => array(
                '-1' => Mage::helper('productreports')->__('Select DC Status')
            ),
            "values" => $dcstatus,
            "value" => $opt_val_ls
        ));
        
        $fieldset->addField("latest_status", "select", array(
            "label" => Mage::helper("productreports")->__("Latest Status"),
            "name" => "latest_status",
            "values" => $this->getLatestStatuses(),
            "options" => array(
                '0' => Mage::helper('productreports')->__('Select Latest Status')
            ),
        ));
		
		$fieldset->addField('sku', 'text', array(
            'name' => 'sku',
            'label' => Mage::helper('productreports')->__('Sku'),
        ));
        
        $fieldset->addField('order_id', 'text', array(
            'name' => 'order_id',
            'label' => Mage::helper('productreports')->__('Order ID'),
        ));
        
		$fieldset->addField('product_name', 'text', array(
            'name' => 'product_name',
            'label' => Mage::helper('productreports')->__('Product Name'),
        ));
        $fieldset->addField('customer_email', 'text', array(
            'name' => 'customer_email',
            'label' => Mage::helper('productreports')->__('Customer Email'),
        ));

        $fieldset->addField('coupon_code', 'text', array(
            'name' => 'coupon_code',
            'label' => Mage::helper('productreports')->__('Coupon Code'),
        ));

        $fieldset->addField('customer_postcode', 'text', array(
            'name' => 'customer_postcode',
            'label' => Mage::helper('productreports')->__('Customer Postcode'),
        ));

        $fieldset->addField('customer_state', 'text', array(
            'name' => 'customer_state',
            'label' => Mage::helper('productreports')->__('Customer State'),
        ));

        $courier = Mage::getModel("provider/provider")->getDropDownOptions();
        array_unshift($courier, "Select Courier Provider");

        $fieldset->addField('courier', 'select', array(
            'name' => 'courier',
            'values' => $courier,
            'label' => Mage::helper('productreports')->__('Courier'),
            'title' => Mage::helper('productreports')->__('Courier'),
            "options" => array(
                '' => Mage::helper('productreports')->__('Select Courier Provider')
            ),
        ));
		
		$productTypeArr = array('configurable'=>'Orders', 'giftcard'=>'Giftcard', 'all'=>'All Orders');
		$fieldset->addField('product_type', 'select', array(
            'name' => 'product_type',
            'values' => $productTypeArr,
            'label' => Mage::helper('productreports')->__('Order Type'),
            'title' => Mage::helper('productreports')->__('Order Type'),
            "options" => array(
                '-1' => Mage::helper('productreports')->__('Order Type')
            ),
        ));
        
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getActivPaymentMethods() {
        $payments = Mage::getSingleton('payment/config')->getActiveMethods();
        $methods = array();

        $methods[''] = "Select Payment Gateway";
        foreach ($payments as $paymentCode => $paymentModel) {
            if ($paymentCode != 'free') {
                $paymentTitle = Mage::getStoreConfig('payment/' . $paymentCode . '/title');
                $methods[$paymentCode] = array(
                    'label' => $paymentTitle,
                    'value' => $paymentCode,
                );
            }
        }
        return $methods;
    }

    public function getDcStatuses() {

        $statuses = array();

        $statuses = array(
            '-1' => 'Select DC Status',
            '0' => 'Not Sent to DC',
            '1' => 'Sent to DC',
            '2' => 'Confirmed',
            '3' => 'Rejected',
            '4' => 'Shipped',
            '5' => 'Delivered',
            '6' => 'Not Delivered',
            '7'=>'Partial Shipped'
        );

        return $statuses;
    }

    public function getLatestStatuses() {

        $statuses = array();

        $statuses['0'] = 'Select Latest Status';

        $order_status_arr = Mage::getSingleton('sales/order_config')->getStatuses();

        foreach($order_status_arr as $key => $value){
            $statuses[$key] = $value;
        }

        return $statuses;
    }

}
