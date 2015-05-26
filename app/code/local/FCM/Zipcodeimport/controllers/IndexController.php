<?php

/**
 * FCM Zip Code Import Module 
 *
 * Module for importing zip code, city and state for address verification.
 *
 * @category    FCM
 * @package     FCM_Zipcodeimport
 * @author	Vikrant Kumar Mishra
 * @author_id	51402601
 * @company	HCL Technologies
 * @created Thursday, June 7, 2012
 */
class FCM_Zipcodeimport_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function getStateCityAction() {

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');

        $_quote_billing = Mage::getSingleton("checkout/session")->getQuote()->getBillingAddress();

        $_quote_shipping = Mage::getSingleton("checkout/session")->getQuote()->getShippingAddress();

        $billing_zip_code = $_quote_billing->getPostcode();

        if ($billing_zip_code != "") {
            $billing_zip_values = Mage::getResourceModel('zipcodeimport/zipcodeimport_collection')
                            ->addFieldToFilter('zip_code', array('eq' => $billing_zip_code));

            $billing_zip_values_array = $billing_zip_values->getData();

            $query_bill = 'SELECT region_id FROM ' . $resource->getTableName('directory_country_region') . ' WHERE default_name = "' . $billing_zip_values_array[0]['state'] . '" ';

            $result_bill = $readConnection->fetchRow($query_bill);
        }

        $shipping_zip_code = $_quote_shipping->getPostcode();

        if ($shipping_zip_code != "") {

            $shipping_zip_values = Mage::getResourceModel('zipcodeimport/zipcodeimport_collection')->addFieldToFilter('zip_code', array('eq' => $shipping_zip_code));

            $shipping_zip_values_array = $shipping_zip_values->getData();

            $query_ship = 'SELECT region_id FROM ' . $resource->getTableName('directory_country_region') . ' WHERE default_name = "' . $shipping_zip_values_array[0]['state'] . '" ';

            $result_ship = $readConnection->fetchRow($query_ship);
        }

        $myarray = array(
            'billing' => array(
                'postcode' => $billing_zip_code,
                'city' => $billing_zip_values_array[0]['city'],
                'state' => $result_bill['region_id'],
                'statetext' => $billing_zip_values_array[0]['state']
            ),
            'shipping' => array(
                'postcode' => $shipping_zip_code,
                'city' => $shipping_zip_values_array[0]['city'],
                'state' => $result_ship['region_id'],
                'statetext' => $shipping_zip_values_array[0]['state']
            )
        );

        //pr($myarray);
        echo json_encode($myarray);
    }

    public function getCodAvailabilityAction() {

        $zipcode = $this->getRequest()->getParam("zip_code");

        if ($zipcode == '' || $zipcode == 'Enter Your Pin') {
            echo "Please enter a valid PIN CODE to check availablity.";
            return;
        }
		$wronpincode = Mage::getStoreConfig('zipcodeimport/cod/wrong_pincode_msg');
		
        $zipcodeimportModel = Mage::getModel('zipcodeimport/zipcodeimport')->getCollection()->addFieldToFilter('zip_code', array('like' => $zipcode));
        $zipcode_data = $zipcodeimportModel->getData();
		
		if(is_array($zipcode_data) && sizeof($zipcode_data) == 0){
			echo $wronpincode;
			return;
		}
        $flag = $zipcode_data['0']['cod'];

        if ($flag) {
            $x = Mage::getStoreConfig('zipcodeimport/cod/min_amount');
            $y = Mage::getStoreConfig('zipcodeimport/cod/max_amount');

            if ($x != '' && $y != '') {
                $search = array('X', 'Y');
                $replace = array($x, $y);
                $subject = Mage::getStoreConfig('zipcodeimport/cod/availabile_msg');
                $return = str_replace($search, $replace, $subject);
            } else {
                $return = 'Cash on Delivery is available for your PIN CODE.';
            }
        } else {
            $return = Mage::getStoreConfig('zipcodeimport/cod/not_availabile_msg');
        }

        echo $return;
    }

}