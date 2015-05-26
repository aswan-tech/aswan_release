<?php

/**
 * Magento Block to fetch the info from DB to track the order status
 *
 * This defines functions to verify the credentials (like mobileNumber and orderID etc.) enterd by the user to Track the order info.
 *
 * @category    FCM
 * @package     FCM_Trackorder
 * @author		Vishal Verma
 * @author_id	51427958
 * @company		HCL Technologies
 * @created 	Friday, August 10, 2012
 * @copyright	Four cross media
 */

/**
 * Block to verify the credentials enter by the user
 *
 * @category    FCM
 * @package     FCM_Trackorder
 * @author      Vishal Verma <51427958>
 */
class FCM_Trackorder_Block_Trackorder extends Mage_Core_Block_Template {
    /*
     * To allow caching added this method
     *
     */

    protected function _construct() {
        $this->addData(array(
            'cache_lifetime' => null,
            'cache_tags' => array(FCM_Trackorder_Model_Trackorder::CACHE_TAG),
            'cache_key' => 'track_order_key',
        ));
    }

    /*
     * To show the breadcrumb into my account section
     *
     */

    public function _prepareLayout() {
        ############# Adding Breadcrumbs -- Source http://www.magestore.com/blog/2010/04/17/add-custom-breadcrumbs-to-any-pages ##############
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        $breadcrumbs->addCrumb('home', array('label' => Mage::helper('cms')->__('Home'), 'title' => Mage::helper('cms')->__('Home Page'), 'link' => Mage::getBaseUrl()));
        $breadcrumbs->addCrumb('myaccount', array('label' => Mage::helper('cms')->__('My Account'), 'title' => Mage::helper('cms')->__('My Account'), 'link' => Mage::getUrl("customer/account")));
        $breadcrumbs->addCrumb('trackorder', array('label' => 'Track My Order', 'title' => 'Track My Order'));


        return parent::_prepareLayout();
    }

    /*
     * To set the session
     *
     */

    public function getTrackorder() {
        if (!$this->hasData('trackorder')) {
            $this->setData('trackorder', Mage::registry('trackorder'));
        }
        return $this->getData('trackorder');
    }

    /*
     * To get the form action depending on the logged-in user or the guest
     *
     */

    public function getPostUrl() {
        //return $this->getUrl('trackorder/index/detail', array('mobile' => $this->getRequest()->getParam('mobile'), 'trackid' => $this->getRequest()->getParam('trackID'), 'ordno' => $this->getRequest()->getParam('ordNo')));
        if (Mage::getSingleton('customer/session')->isLoggedIn())
            return $this->getUrl('trackorder/index/detail');
        else
            return $this->getUrl('trackorder/index/orderdetail');
    }

    /*
     * To verify, if the mobileNumber and the TrackingID/OrderID exist, if exists, check if orderID exists, if all fine, process DB and return data
     *
     */

    public function getTrackDetails() {
        $params = $this->getRequest()->getParams();

        //$post = $this->getRequest()->getPost();
        $confModel = Mage::getModel('trackorder/trackorder');

        if ($params['mobile'] == '') {
            //Source http://subesh.com.np/2010/03/redirect-location-model-observer-magento/
            if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
                Mage::getSingleton('core/session')->addError($this->__('Please enter mobile number.'));
                Mage::app()->getResponse()->setRedirect(Mage::getUrl("*/*/guest", $params));
            } else {
                Mage::getSingleton('customer/session')->addError($this->__('Please enter mobile number.'));
                Mage::app()->getResponse()->setRedirect(Mage::getUrl("*/*/index", $params));
            }
            return;
        }
        if ($params['trackID'] != '') {
            //############ TrackID can be any, "Airwaybill Number" or "Reference Number"	############
            //check for the combination of TrackID and Mobile Number
            $query = "SELECT oa.entity_id FROM sales_flat_shipment_track st inner join sales_flat_order_address oa on (st.order_id=oa.parent_id) WHERE track_number='" . $params['trackID'] . "' and telephone='" . $params['mobile'] . "' and address_type='billing'";
            $data = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($query);

            if (count($data) == 0) {
                //Source http://subesh.com.np/2010/03/redirect-location-model-observer-magento/
                if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
                    Mage::getSingleton('core/session')->addError($this->__('No record found for mobile "' . $params['mobile'] . '" and tracking id "' . $params['trackID'] . '".'));
                    Mage::app()->getResponse()->setRedirect(Mage::getUrl("*/*/guest", $params));
                } else {
                    Mage::getSingleton('customer/session')->addError($this->__('No record found for mobile "' . $params['mobile'] . '" and tracking id "' . $params['trackID'] . '".'));
                    Mage::app()->getResponse()->setRedirect(Mage::getUrl("*/*/index", $params));
                }
                return;
            } else {
                $_trackModel = Mage::getModel('sales/order_shipment_track')->load($params['trackID'], 'track_number');
                $_trackDetails = array();
                $_trackDetails['number'] = $_trackModel->getTrackNumber();
                $_trackDetails['name'] = $_trackModel->getTitle();
            }
        } elseif ($params['ordNo'] != '') {
            $order = Mage::getModel('sales/order')->loadByIncrementId($params['ordNo']);
            $_trackDetails = array();

            //Check if OrderID exists
            if ($order->getId()) {
                $actMobile = $order->getBillingAddress()->getTelephone();
                #######		Get the Tracking details of this OrderID	#######
                $_trackDetails = array();
                $shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')->setOrderFilter($order)->load();
                foreach ($shipmentCollection as $shipment) {
                    foreach ($shipment->getAllTracks() as $tracknum) {
                        $_trackDetails['name'] = $tracknum->getTitle();
                        $_trackDetails['number'] = $tracknum->getTrackNumber();
                    }
                }
                #######		Get the Tracking details of this OrderID	#######
            } else {
                $actMobile = 0;
                if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
                    Mage::getSingleton('core/session')->addError($this->__('Order number "' . $params['ordNo'] . '" does not exist.'));
                    Mage::app()->getResponse()->setRedirect(Mage::getUrl("*/*/guest", $params));
                } else {
                    Mage::getSingleton('customer/session')->addError($this->__('Order number "' . $params['ordNo'] . '" does not exist.'));
                    Mage::app()->getResponse()->setRedirect(Mage::getUrl("*/*/index", $params));
                }
                return;
            }

            //Check for the combination of OrderID and Mobile Number, if OrderID exists
            if ($params['mobile'] != $actMobile) {
                if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
                    Mage::getSingleton('core/session')->addError($this->__('No record found for mobile "' . $params['mobile'] . '" and order number "' . $params['ordNo'] . '".'));
                    Mage::app()->getResponse()->setRedirect(Mage::getUrl("*/*/guest", $params));
                } else {
                    Mage::getSingleton('customer/session')->addError($this->__('No record found for mobile "' . $params['mobile'] . '" and order number "' . $params['ordNo'] . '".'));
                    Mage::app()->getResponse()->setRedirect(Mage::getUrl("*/*/index", $params));
                }
                return;
            }

            //If combination of OrderID and Mobile exists, check if tracking number has been generated
            if (empty($_trackDetails)) {
                //Source http://subesh.com.np/2010/03/redirect-location-model-observer-magento/
                if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
                    Mage::getSingleton('core/session')->addError("No tracking information found for order number " . $params['ordNo'] . " as your order has not been shipped yet.");
                    Mage::app()->getResponse()->setRedirect(Mage::getUrl("*/*/guest", $params));
                } else {
                    Mage::getSingleton('customer/session')->addError("No tracking information found for order number " . $params['ordNo'] . " as your order has not been shipped yet.");
                    Mage::app()->getResponse()->setRedirect(Mage::getUrl("*/*/index", $params));
                }
                return;
            }
        }

        try {
            if ($_trackDetails['name'] != "Blue Dart" && $_trackDetails['name'] != "Aramex") {
                $_redirectUrlProvider = '';
                $shippingProvider = $_trackDetails['name'];
                $_providerLoad = Mage::getModel("provider/provider")->load($shippingProvider, 'shippingprovider_name');

                $_redirectUrlProvider = $_providerLoad->getShippingproviderAction();

                if (filter_var($_redirectUrlProvider, FILTER_VALIDATE_URL) === FALSE) {
                    if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                        Mage::getSingleton('customer/session')->addError("Wrong redirection URL provided in Admin for Shipping Provider : " . $shippingProvider);
                        Mage::app()->getResponse()->setRedirect(Mage::getUrl("*/*/index", $params));
                    } else {
                        Mage::getSingleton('core/session')->addError("Wrong redirection URL provided in Admin for Shipping Provider : " . $shippingProvider);
                        Mage::app()->getResponse()->setRedirect(Mage::getUrl("*/*/guest", $params));
                    }
                } else {
					//pr($_trackDetails,0);pr($_redirectUrlProvider);
					if(strtolower($_trackDetails['name']) == 'fedex'){
						$_redirectUrlProvider .= $_trackDetails['number'];
						Mage::app()->getResponse()->setRedirect($_redirectUrlProvider);
					}else{
						Mage::app()->getResponse()->setRedirect($_redirectUrlProvider);
					}
                }
            } else {
                if ($_trackDetails['name'] == "Aramex") {
                    $ordDtls = $confModel->getAramexOrderDetails($_trackDetails['number']);

                    if ($params['trackID'] != '') {
                        $order = Mage::getModel('sales/order')->load($_trackModel->getOrderId());
                        $ordDtls['OrderNumber'] = "#" . $order->getIncrementId();
                    } else {
                        $ordDtls['OrderNumber'] = "#" . $order->getId();
                    }

                    $shipping_address = $order->getShippingAddress()->getData();
                    $billing_address = $order->getBillingAddress()->getData();

                    $ordDtls['OrderDate_Formatted'] = date("m/d/Y", strtotime($order->getCreatedAt()));
                    $ordDtls['Origin'] = $billing_address['firstname'] . " " . $billing_address['lastname'] . ", " . $billing_address['city'] . ", " . $billing_address['region'] . ", " . $billing_address['postcode'];
                    $ordDtls['Destination'] = $shipping_address['firstname'] . " " . $shipping_address['lastname'] . ", " . $shipping_address['city'] . ", " . $shipping_address['region'] . ", " . $shipping_address['postcode'];
                } elseif (strtolower($_trackDetails['name']) == "fedex") {
                    /*
					$shipTrack = array();
                    if ($params['trackID'] != '') {
                        $order = Mage::getModel('sales/order')->load($_trackModel->getOrderId());
                        $ordDtls['OrderNumber'] = "#" . $order->getIncrementId();
                    } else {
                        $ordDtls['OrderNumber'] = "#" . $order->getId();
                    }

                    if ($order) {
                        $shipments = $order->getShipmentsCollection();
                        foreach ($shipments as $shipment) {
                            $increment_id = $shipment->getIncrementId();
                            $tracks = $shipment->getTracksCollection();

                            $trackingInfos = array();
                            foreach ($tracks as $track) {
                                $trackingInfos[] = $track->getNumberDetail();
                            }
                            $shipTrack[$order->getIncrementId()] = $trackingInfos;
                        }

                        $shipping_address = $order->getShippingAddress()->getData();
                        $billing_address = $order->getBillingAddress()->getData();

                        $ordDtls['OrderDate_Formatted'] = date("m/d/Y", strtotime($order->getCreatedAt()));
                        $ordDtls['Origin'] = $billing_address['firstname'] . " " . $billing_address['lastname'] . ", " . $billing_address['city'] . ", " . $billing_address['region'] . ", " . $billing_address['postcode'];
                        $ordDtls['Destination'] = $shipping_address['firstname'] . " " . $shipping_address['lastname'] . ", " . $shipping_address['city'] . ", " . $shipping_address['region'] . ", " . $shipping_address['postcode'];

                        $ordDtlsFinal = $shipTrack[$order->getIncrementId()][0]->getData();
                        $progressdetail = $ordDtlsFinal['progressdetail'];
                        foreach ($progressdetail as $progress) {
                            if ($progress['activity'] == 'Picked up') {
                                $ordDtls['PickUpDate_Formatted'] = date("m/d/Y", strtotime($progress['deliverydate']));
                            }
                        }

                        $ordDtls['WaybillNo'] = $ordDtlsFinal['tracking'];
                        $ordDtls['Status'] = $ordDtlsFinal['status'];
                        $ordDtls['StatusDate_Formatted'] = date("m/d/Y", strtotime($ordDtlsFinal['deliverydate']));
                        $ordDtls['StatusTime_Formatted'] = date("H:i:s", strtotime($ordDtlsFinal['deliverytime']));
                    }
					*/
                } else {
                    $ordDtls = $confModel->getOrderDetails($_trackDetails['number']);
                }
                return $ordDtls;
            }
        } catch (Exception $e) {
            if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                Mage::getSingleton('customer/session')->addError($this->__($e->getMessage()));
                Mage::getSingleton('customer/session')->addError("Unable to load the XML file to read1.");
                Mage::app()->getResponse()->setRedirect(Mage::getUrl("*/*/index", $params));
            } else {
                Mage::getSingleton('core/session')->addError("Unable to load the XML file to read2.");
                Mage::app()->getResponse()->setRedirect(Mage::getUrl("*/*/guest", $params));
            }
        }
    }

}