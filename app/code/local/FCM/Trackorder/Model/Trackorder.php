<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

/**
 * Magento Model to process the XML file
 *
 * Magento Model to fetch the data of a trackingID/OrderNumber via cURL and process the returned XML file
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
 * Model to process the XML file after the cURL hit
 *
 * @category    FCM
 * @package     FCM_Trackorder
 * @author      Vishal Verma <51427958>
 */
class FCM_Trackorder_Model_Trackorder extends Mage_Core_Model_Abstract {
    const CACHE_TAG = 'track_order';

    private $outputArr;

    /*
     * Varien style Constructor
     *
     */

    public function _construct() {
        parent::_construct();
        $this->_init('trackorder/trackorder');
    }

    /*
     * This class Constructor which can not be called without above call (It's a Magento std.)
     * It checks for the cosnerned directories to be used, if not there, creates them
     *
     */

    public function __construct() {
        $this->apiUrl = 'http://www.bluedart.com/servlet/RoutingServlet?';

        $this->dfltHandler = 'Tnt';
        $this->dfltAction = 'custawbquery';
        //$this->dfltVersion 	= '1.3';
        $this->dfltScan = '1';
        //$this->dfltAwb 		= 'awb';//ref, awb
        $this->dfltFormat = 'xml'; //xml, html, text

        $this->loginId = Mage::getStoreConfig('trackorder/trackorderdtls/user_id');
        $this->licenseKey = Mage::getStoreConfig('trackorder/trackorderdtls/license_key');
        $this->dfltAwb = Mage::getStoreConfig('trackorder/trackorderdtls/code_type');
        $this->dfltVersion = Mage::getStoreConfig('trackorder/trackorderdtls/api_version');

        if ($this->dfltAwb == '' || ($this->dfltAwb != 'ref' && $this->dfltAwb != 'awb')) {
            $this->dfltAwb = 'awb';
        }

        if ($this->dfltVersion == '') {
            $this->dfltVersion = '1.3';
        }

        $this->xmlTagsToConsider = array(
            'PickUpDate',
            'ExpectedDelivery',
            'Origin',
            'Destination',
            'CustomerName',
            'SenderName',
            'Consignee',
            'ConsigneeAddress1',
            'ConsigneePincode',
            'Status',
            'StatusType',
            'StatusDate',
            'StatusTime',
            'NewWaybillNo',
            'ReceivedBy'
        );

        $this->xmlOrderStatusCodes = array(
            'NF' => 'No Info',
            'IT' => 'Shipment In Transit',
            'UD' => 'Shipment Undelivered',
            'DL' => 'Shipment Delivered',
            'RD' => 'Shipment Redirected',
            'RT' => 'Shipment Returned'
        );
    }

    /*
     * Function to process the return XML file after a cURL call (for Blue-Dart system)
     * 
     */

    public function getOrderDetails($waybill_ref_number) {
        $number = $waybill_ref_number;

        //$callUrl	=	$this->apiUrl."handler=".$this->dfltHandler."&action=".$this->dfltAction."&loginid=".$this->loginId."&awb=".$this->dfltAwb."&numbers=".$this->dfltAwb."&format=".$this->dfltFormat."&lickey=".$this->licenseKey."&verno=".$this->dfltVersion."&scan=".$this->dfltScan;

        $callUrl = "http://www.bluedart.com/servlet/RoutingServlet?handler=tnt&action=custawbquery&loginid=" . $this->loginId . "&awb=" . $this->dfltAwb . "&numbers=" . $number . "&format=xml&lickey=" . $this->licenseKey . "&verno=" . $this->dfltVersion . "&scan=1";

        ################################ hit the curl to get the order status XML file	################################
        $ch = curl_init();
        $timeout = 15;
        curl_setopt($ch, CURLOPT_URL, $callUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

        $data = curl_exec($ch);
        //var_dump(curl_getinfo($ch));
        //var_dump($data);
        curl_close($ch);

        //print $data;die;

        if (!empty($data)) {
            $doc = new DOMDocument();
            try {
                if ($doc->loadXML($data)) {
                    $shipments = $doc->getElementsByTagName("Shipment"); //ShipmentData
                    $i = 0;

                    if ($this->dfltAwb == 'awb') {
                        $checkCol = 'WaybillNo';
                    } else {
                        $checkCol = 'RefNo';
                    }

                    $this->outputArr = array();

                    foreach ($shipments as $shipment) {
                        if ($shipment->getAttribute($checkCol) == $number) {
                            ################ get "order_id" for this TrackID ################
                            $query = "SELECT order_id FROM sales_flat_shipment_track WHERE track_number='" . $number . "'";
                            $data = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchRow($query);

                            $order = Mage::getModel('sales/order')->load($data['order_id']);
                            $this->outputArr['OrderNumber'] = $order->getIncrementId();
                            $this->outputArr['OrderDate'] = $order->getCreatedAt();

                            $orderDateStrToTime = strtotime($order->getCreatedAt());
                            $this->outputArr['OrderDate_Formatted'] = date("d-m-Y h:i A", $orderDateStrToTime);

                            $this->outputArr['WaybillNo'] = $shipment->getAttribute("WaybillNo");
                            $this->outputArr['RefNo'] = $shipment->getAttribute("RefNo");

                            foreach ($this->xmlTagsToConsider as $thisTag) {
                                $this->outputArr[$thisTag] = $shipment->getElementsByTagName($thisTag)->item(0)->nodeValue;

                                if ($thisTag == 'PickUpDate' || $thisTag == 'StatusDate') {
                                    $tagStrToTime = strtotime($this->outputArr[$thisTag]);
                                    $this->outputArr[$thisTag . '_Formatted'] = date("d-m-Y", $tagStrToTime);
                                }

                                if ($thisTag == 'StatusTime') {
                                    $tagStrToTime2 = strtotime($this->outputArr['StatusDate'] . " " . $this->outputArr[$thisTag]);
                                    $this->outputArr['StatusTime_Formatted'] = date("h:i A", $tagStrToTime2);
                                }

                                if ($thisTag == 'StatusType' && ($this->outputArr[$thisTag] == 'RD' || $this->outputArr[$thisTag] == 'RT')) {
                                    $this->outputArr['NewWaybillNo'] = $shipment->getElementsByTagName('NewWaybillNo')->item(0)->nodeValue;
                                }

                                if ($thisTag == 'StatusType' && $this->outputArr[$thisTag] == 'IT') {
                                    $this->outputArr['ExpectedDelivery'] = $shipment->getElementsByTagName('ExpectedDelivery')->item(0)->nodeValue;
                                }
                            }
                        }
                    }
                } else {
                    Mage::getSingleton('customer/session')->addError($this->__('Unable to load the XML file to read3.'));
                    //Source http://subesh.com.np/2010/03/redirect-location-model-observer-magento/
                    Mage::app()->getResponse()->setRedirect(Mage::getUrl("*/*/index"));
                }
            } catch (Exception $e) {
                Mage::getSingleton('customer/session')->addError($this->__($e->getMessage()));
                //Source http://subesh.com.np/2010/03/redirect-location-model-observer-magento/
                Mage::app()->getResponse()->setRedirect(Mage::getUrl("*/*/index"));
            }

            return $this->outputArr;
        } else {
            Mage::getSingleton('customer/session')->addError($this->__("Cannot open the local directory for reading files"));
            //Source http://subesh.com.np/2010/03/redirect-location-model-observer-magento/
            Mage::app()->getResponse()->setRedirect(Mage::getUrl("*/*/index"));
        }
    }

    public function getAramexOrderDetails($waybill_ref_number) {

        $params = array(
            'ClientInfo' => array(
                'AccountCountryCode' => 'IN',
                'AccountEntity' => 'DEL',
                'AccountNumber' => '50614093',
                'AccountPin' => '115216',
                'UserName' => 'service@americanswan.com',
                'Password' => 'swan#1234',
                'Version' => 'v1.0'
            ),
            'Shipments' => array(
                $waybill_ref_number
            )
        );

        $wsdlPath = 'shipments-tracking-api-wsdl.wsdl';

        $soapClient = new SoapClient($wsdlPath);
//        $soapClient = new SoapClient($wsdlPath, array('proxy_host' => "10.112.62.78",
//                    'proxy_port' => 8080,
//                    'proxy_login' => "shikha.r@hcl.com",
//                    'proxy_password' => ""));

        //calling the method and printing results
        try {
            $auth_call = $soapClient->TrackShipments($params);
            $ordDtlsArr = $auth_call->TrackingResults->KeyValueOfstringArrayOfTrackingResultmFAkxlpY->Value->TrackingResult;

            $ordDtlsFinal = $ordDtlsArr[0];

            end($ordDtlsArr);         // move the internal pointer to the end of the array
            $key = key($ordDtlsArr);
            $ordDtlsStart = $ordDtlsArr[$key];

            $ordDtls['WaybillNo'] = $ordDtlsFinal->WaybillNumber;
            $ordDtls['PickUpDate_Formatted'] = date("m/d/Y", strtotime($ordDtlsStart->UpdateDateTime));

            $ordDtls['Status'] = $ordDtlsFinal->UpdateDescription;
            $ordDtls['StatusDate_Formatted'] = date("m/d/Y", strtotime($ordDtlsFinal->UpdateDateTime));
            $ordDtls['StatusTime_Formatted'] = date("H:i:s", strtotime($ordDtlsFinal->UpdateDateTime));

            return $ordDtls;
        } catch (SoapFault $fault) {
            $string = 'faultcode:' . $fault->faultcode . '<br>' .
                    'faultstring:' . $fault->faultstring . '<br>' .
                    'faultactor:' . $fault->faultactor . '<br>' .
                    'detail:' . $fault->detail . '<br>' .
                    'faultname:' . $fault->faultname . '<br>' .
                    'headerfault:' . $fault->headerfault;
            Mage::log('Exception of armaex api: ' . $string);
        }
    }

}